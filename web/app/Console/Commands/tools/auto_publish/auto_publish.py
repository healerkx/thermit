# -*- coding:utf-8 -*-  

import sys, os, json
import platform
if platform.system() == 'Darwin':
    sys.path.append('/usr/local/lib/python2.7/site-packages')

import requests
import config
import hashlib
from pyquery import PyQuery as pq

session = requests.Session()
session.trust_env = False

# wiki登录
def wiki_login():
    wiki_login_url = "http://wiki.atlassian.com/dologin.action"
    user_data = config.user_data()

    login_response = session.post(wiki_login_url, data=user_data)

    login_content = login_response.content
    pq_login = pq(login_content)
    login_error = pq_login.find(".aui-message-error li").html()
    if (login_error):
        print('login error')
        exit()

    cookie = login_response.headers.get('set-cookie')

    return cookie

# 打开本地文件
def open_file(file_name):
    print(file_name)
    file_object = open(file_name)
    try:
         all_the_text = file_object.read( )
    finally:
         file_object.close()

    return all_the_text


def edit_wiki(page_id, file_name):
    cookie = wiki_login()
    headers = {
        "Cookie": cookie
    }

    content = open_file(file_name)
    md5obj = hashlib.md5()
    md5obj.update(content.encode('utf-8'))
    hashval = md5obj.hexdigest()

    hash_file = file_name + ".md5"
    if not os.path.exists(hash_file):
        file = open(hash_file, 'w')
        file.write('')
        file.close()

    file = open(hash_file, 'r')
    if hashval == file.readline():
        file.close()
        print("No need to update %s" % file_name.split('/')[-1])
        return False
    else:
        file.close()
        file = open(hash_file, 'w')
        file.write(hashval)
        file.close()


    # 获取所需参数
    detail_url = "http://wiki.atlassian.com/rest/tinymce/1/content/%s.json?_=1489566702618" % page_id
    detail_response = session.get(detail_url, headers=headers)
    print(detail_response)
    detail = json.loads(str(detail_response.text))

    edit_url = "http://wiki.atlassian.com/pages/doeditpage.action?pageId=%s" % page_id
    data = {
        'title' : detail['title'],
        'wysiwygContent' : content,
        'notifyWatchers':'true',
        'draftId':0,
        'originalVersion':int(detail['pageVersion']),
        'atl_token':detail['atlToken']
    }
    edit_response = session.post(edit_url, headers=headers, data=data)

    print("Update Success %s" % file_name.split('/')[-1])

if __name__ == '__main__':
    user_data = config.user_data()
    file_infos = config.file_to_update()
    for file_info in file_infos: 
        edit_wiki(file_info, file_infos[file_info])
