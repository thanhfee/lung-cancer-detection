import argparse
import json
import random
from pathlib import Path

import numpy as np
import tensorflow as tf


ROOT = Path(__file__).resolve().parent
IMAGE_DIR = ROOT / "data" / "images"
OUT_DIR = ROOT / "model_output" / "transfer_mobilenetv2"
LABELS = {"normal": 0, "sick": 1}
SEED = 42


def collect_split(dev_ratio):
    rng = random.Random(SEED)
    train_paths, train_labels = [], []
    dev_paths, dev_labels = [], []

    for class_name, label in LABELS.items():
        files = sorted(
            p for p in (IMAGE_DIR / class_name).iterdir()
            if p.suffix.lower() in {".jpg", ".jpeg", ".png"}
        )
        rng.shuffle(files)
        dev_count = max(1, int(round(len(files) * dev_ratio)))
        dev = files[:dev_count]
        train = files[dev_count:]

        train_paths.extend(str(p) for p in train)
        train_labels.extend(label for _ in train)
        dev_paths.extend(str(p) for p in dev)
        dev_labels.extend(label for _ in dev)

    train_pairs = list(zip(train_paths, train_labels))
    dev_pairs = list(zip(dev_paths, dev_labels))
    rng.shuffle(train_pairs)
    rng.shuffle(dev_pairs)

    return zip(*train_pairs), zip(*dev_pairs)


def make_dataset(paths, labels, batch_size, training):
    paths = list(paths)
    labels = list(labels)
    ds = tf.data.Dataset.from_tensor_slices((paths, labels))
    if training:
        ds = ds.shuffle(len(paths), seed=SEED, reshuffle_each_iteration=True)

    def load_image(path, label):
        image = tf.io.read_file(path)
        image = tf.io.decode_image(image, channels=3, expand_animations=False)
        image = tf.image.resize(image, (224, 224))
        image = tf.cast(image, tf.float32)
        return image, tf.cast(label, tf.int32)

    return ds.map(load_image, num_parallel_calls=tf.data.AUTOTUNE) \
        .batch(batch_size) \
        .prefetch(tf.data.AUTOTUNE)


def class_weights(labels):
    labels = np.asarray(labels)
    total = len(labels)
    return {
        int(label): float(total / (len(LABELS) * np.sum(labels == label)))
        for label in np.unique(labels)
    }


def build_model():
    inputs = tf.keras.Input(shape=(224, 224, 3))
    x = tf.keras.Sequential([
        tf.keras.layers.RandomFlip("horizontal"),
        tf.keras.layers.RandomRotation(0.04),
        tf.keras.layers.RandomZoom(0.08),
        tf.keras.layers.RandomContrast(0.08),
    ], name="augmentation")(inputs)
    x = tf.keras.applications.mobilenet_v2.preprocess_input(x)

    base = tf.keras.applications.MobileNetV2(
        include_top=False,
        weights="imagenet",
        input_shape=(224, 224, 3),
    )
    base.trainable = False

    x = base(x, training=False)
    x = tf.keras.layers.GlobalAveragePooling2D()(x)
    x = tf.keras.layers.Dropout(0.35)(x)
    outputs = tf.keras.layers.Dense(2, activation="softmax")(x)
    model = tf.keras.Model(inputs, outputs)
    return model, base


def compile_model(model, learning_rate):
    model.compile(
        optimizer=tf.keras.optimizers.Adam(learning_rate),
        loss="sparse_categorical_crossentropy",
        metrics=[
            "accuracy",
            tf.keras.metrics.SparseTopKCategoricalAccuracy(k=1, name="top1"),
        ],
    )


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--epochs", type=int, default=8)
    parser.add_argument("--fine-tune-epochs", type=int, default=5)
    parser.add_argument("--batch-size", type=int, default=16)
    parser.add_argument("--dev-ratio", type=float, default=0.2)
    parser.add_argument("--fine-tune-layers", type=int, default=35)
    args = parser.parse_args()

    OUT_DIR.mkdir(parents=True, exist_ok=True)
    (train_paths, train_labels), (dev_paths, dev_labels) = collect_split(args.dev_ratio)
    train_paths, train_labels = list(train_paths), list(train_labels)
    dev_paths, dev_labels = list(dev_paths), list(dev_labels)

    metadata = {
        "labels": {"0": "Benign", "1": "Malignant"},
        "train_count": len(train_labels),
        "dev_count": len(dev_labels),
        "train_labels": {str(i): int(train_labels.count(i)) for i in LABELS.values()},
        "dev_labels": {str(i): int(dev_labels.count(i)) for i in LABELS.values()},
    }
    (OUT_DIR / "metadata.json").write_text(json.dumps(metadata, indent=2), encoding="utf-8")
    print(json.dumps(metadata, indent=2))

    train_ds = make_dataset(train_paths, train_labels, args.batch_size, training=True)
    dev_ds = make_dataset(dev_paths, dev_labels, args.batch_size, training=False)
    weights = class_weights(train_labels)
    print("class_weight:", weights)

    model, base = build_model()
    compile_model(model, 1e-3)
    callbacks = [
        tf.keras.callbacks.ModelCheckpoint(
            str(OUT_DIR / "best.keras"),
            monitor="val_accuracy",
            mode="max",
            save_best_only=True,
        ),
        tf.keras.callbacks.EarlyStopping(
            monitor="val_accuracy",
            mode="max",
            patience=4,
            restore_best_weights=True,
        ),
        tf.keras.callbacks.CSVLogger(str(OUT_DIR / "history.csv")),
    ]

    model.fit(
        train_ds,
        validation_data=dev_ds,
        epochs=args.epochs,
        class_weight=weights,
        callbacks=callbacks,
    )

    if args.fine_tune_epochs > 0:
        base.trainable = True
        for layer in base.layers[:-args.fine_tune_layers]:
            layer.trainable = False
        compile_model(model, 1e-5)
        model.fit(
            train_ds,
            validation_data=dev_ds,
            initial_epoch=args.epochs,
            epochs=args.epochs + args.fine_tune_epochs,
            class_weight=weights,
            callbacks=callbacks,
        )

    best = tf.keras.models.load_model(str(OUT_DIR / "best.keras"))
    results = best.evaluate(dev_ds, verbose=1)
    metrics = dict(zip(best.metrics_names, [float(x) for x in results]))
    (OUT_DIR / "eval.json").write_text(json.dumps(metrics, indent=2), encoding="utf-8")
    best.save(str(OUT_DIR / "medical_model.h5"))
    best.export(str(OUT_DIR / "saved_model"))
    print("final_eval:", json.dumps(metrics, indent=2))


if __name__ == "__main__":
    main()
