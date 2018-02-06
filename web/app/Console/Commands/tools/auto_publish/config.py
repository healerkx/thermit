# -*- coding:utf-8 -*-  

import os

# wiki登录

current_path = os.path.dirname(os.path.abspath(__file__))
doc_path = ''

def user_data():
    global doc_path
    username = ''
    password = ''

    config_path = '%s/config' % current_path
    if os.path.exists(config_path):
        with open(config_path) as file:
            for line in file.readlines():
                if line.startswith('username'):
                    username = line.split('=')[1].strip()
                elif line.startswith('password'):
                    password = line.split('=')[1].strip()
                elif line.startswith('docpath'):
                    doc_path = line.split('=')[1].strip()

    return {
        "os_username" : username,
        "os_password" : password,
        'doc_path': doc_path
    }

# 文件配置
# wiki pageId:本地文件目录
def file_to_update():
    global doc_path
    return {
        15435018 : doc_path + '/KxUser.html',
    }