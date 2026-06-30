@echo off
cd /d C:\xampp\htdocs\Do-an-Laravel\lung-cancer-detection\noisystudent
C:\Users\Admin\AppData\Local\Programs\Python\Python39\python.exe main.py --model_name=efficientnet-b0 --use_tpu=False --use_bfloat16=True --task_name=imagenet --mode=train --train_batch_size=4 --eval_batch_size=8 --iterations_per_loop=1000 --save_checkpoints_steps=1000 --train_steps=20000 --steps_per_eval=3000 --num_label_classes=2 --unlabel_ratio=0 --label_data_dir=data --model_dir=model_output > model_output\train_resume.log 2>&1
