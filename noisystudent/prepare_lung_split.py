import os
import random
from pathlib import Path

import cv2
import tensorflow.compat.v1 as tf


ROOT = Path(__file__).resolve().parent
IMAGE_DIR = ROOT / "data" / "images"
OUTPUT_DIR = ROOT / "data"
IMG_SIZE = 224
SEED = 42
DEV_RATIO = 0.2
LABELS = {"normal": 0, "sick": 1}


def bytes_feature(value):
    return tf.train.Feature(bytes_list=tf.train.BytesList(value=[value]))


def int64_feature(value):
    return tf.train.Feature(int64_list=tf.train.Int64List(value=[value]))


def collect_images():
    by_class = {}
    for label_name in LABELS:
        files = []
        folder = IMAGE_DIR / label_name
        for name in sorted(os.listdir(folder)):
            path = folder / name
            if path.is_file() and path.suffix.lower() in {".jpg", ".jpeg", ".png"}:
                files.append(path)
        by_class[label_name] = files
    return by_class


def split_stratified(by_class):
    rng = random.Random(SEED)
    train = []
    dev = []
    for label_name, files in by_class.items():
        files = files[:]
        rng.shuffle(files)
        dev_count = max(1, int(round(len(files) * DEV_RATIO)))
        dev.extend((path, LABELS[label_name]) for path in files[:dev_count])
        train.extend((path, LABELS[label_name]) for path in files[dev_count:])
    rng.shuffle(train)
    rng.shuffle(dev)
    return train, dev


def oversample_minority(train):
    rng = random.Random(SEED)
    by_label = {}
    for path, label in train:
        by_label.setdefault(label, []).append((path, label))

    max_count = max(len(items) for items in by_label.values())
    balanced = []
    for items in by_label.values():
        balanced.extend(items)
        balanced.extend(rng.choices(items, k=max_count - len(items)))

    rng.shuffle(balanced)
    return balanced


def write_tfrecord(samples, output_path):
    with tf.io.TFRecordWriter(str(output_path)) as writer:
        for path, label in samples:
            image = cv2.imread(str(path))
            if image is None:
                raise RuntimeError(f"Cannot read image: {path}")
            image = cv2.resize(image, (IMG_SIZE, IMG_SIZE))
            ok, encoded = cv2.imencode(".jpg", image)
            if not ok:
                raise RuntimeError(f"Cannot encode image: {path}")
            example = tf.train.Example(features=tf.train.Features(feature={
                "image/encoded": bytes_feature(encoded.tobytes()),
                "image/class/label": int64_feature(label),
            }))
            writer.write(example.SerializeToString())


def summarize(name, samples):
    counts = {}
    for _, label in samples:
        counts[label] = counts.get(label, 0) + 1
    print(f"{name}: total={len(samples)}, labels={counts}")


def main():
    by_class = collect_images()
    train, dev = split_stratified(by_class)
    balanced_train = oversample_minority(train)

    summarize("train_before_balance", train)
    summarize("train_balanced", balanced_train)
    summarize("dev", dev)

    write_tfrecord(balanced_train, OUTPUT_DIR / "train.tfrecord")
    write_tfrecord(dev, OUTPUT_DIR / "dev.tfrecord")


if __name__ == "__main__":
    main()
