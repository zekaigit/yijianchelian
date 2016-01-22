<?php
use sinacloud\sae\Storage as Storage;

**���ʼ��**

// ����һ����SAE���л�����ʱ���Բ�����֤��Ϣ��Ĭ�ϻ��Ӧ�õĻ���������ȡ
$s = new Storage();

// ���������������SAE���л�������Ҫ���Ǳ�Ӧ�õ�storage����Ҫ��������Ӧ�õ�"Ӧ����:Ӧ��AccessKey"��"Ӧ��SecretKey"
$s = new Storage("$AppName:$AccessKey", $SecretKey);

**Bucket����**

// ����һ��Bucket test
$s->putBucket("test");

// ��ȡBucket�б�
$s->listBuckets();

// ��ȡBucket�б�Bucket��Object������Bucket�Ĵ�С
$s->listBuckets(true);

// ��ȡtest���Bucket�е�Object�����б�Ĭ�Ϸ���ǰ1000���������Ҫ���ش���1000��Object���б�����ͨ��limit������ָ����
$s->getBucket("test");

// ��ȡtest���Bucket�������� *a/* Ϊǰ׺��Objects�б�
$s->getBucket("test", 'a/');

// ��ȡtest���Bucket�������� *a/* Ϊǰ׺��Objects�б�ֻ��ʾ *a/N* ���Object֮����б������� *a/N* ���Object����
$s->getBucket("test", 'a/', 'a/N');

// StorageҲ���Ե���һ��α�ļ�ϵͳ��ʹ�ã������ȡ *a/* Ŀ¼�µ�Object������ʾ���µ���Ŀ¼�ľ���Object���ƣ�ֻ��ʾĿ¼����
$s->getBucket("test", 'a/', null, 10000, '/');

// ɾ��һ���յ�Bucket test
$s->deleteBucket("test");

**Object�ϴ�����**

// ��$_FILESȫ�ֱ����еĻ����ļ��ϴ���test���Bucket�����ô�Object��Ϊ1.txt
$s->putObjectFile($_FILES['uploaded']['tmp_name'], "test", "1.txt");

// ��$_FILESȫ�ֱ����еĻ����ļ��ϴ���test���Bucket�����ô�Object��Ϊsae/1.txt
$s->putObjectFile($_FILES['uploaded']['tmp_name'], "test", "sae/1.txt");

// �ϴ�һ���ַ�����test���Bucket�У����ô�Object��Ϊstring.txt������������Content-type
$s->putObject("This is string.", "test", "string.txt", Storage::ACL_PUBLIC_READ, array(), array('Content-Type' => 'text/plain'));

// �ϴ�һ���ļ������������buffer����һ���ļ����ļ��ᱻ�Զ�fclose������test���Bucket�У����ô�Object��Ϊfile.txt
$s->putObject(Storage::inputResource(fopen($_FILES['uploaded']['tmp_name'], 'rb'), filesize($_FILES['uploaded']['tmp_name']), "test", "file.txt", Storage::ACL_PUBLIC_READ);

**Object���ز���**

// ��test���Bucket��ȡObject 1.txt�����Ϊ�˴��������ϸ��Ϣ������״̬���1.txt�����ݵ�
var_dump($s->getObject("test", "1.txt"));

// ��test���Bucket��ȡObject 1.txt����1.txt�����ݱ�����SAE_TMP_PATH����ָ����TmpFS�У�savefile.txtΪ������ļ���;SAE_TMP_PATH·������дȨ�ޣ��û����������Ŀ¼��д�ļ������ļ����������ڵ�ͬ��PHP����Ҳ���ǵ���PHP�������ִ��ʱ������д��SAE_TMP_PATH���ļ����ᱻ����
$s->getObject("test", "1.txt", SAE_TMP_PATH."savefile.txt");

// ��test���Bucket��ȡObject 1.txt����1.txt�����ݱ����ڴ򿪵��ļ������
$s->getObject("test", "1.txt", fopen(SAE_TMP_PATH."savefile.txt", 'wb'));

**Objectɾ������**

// ��test���Bucketɾ��Object 1.txt
$s->deleteObject("test", "1.txt");

**Object���Ʋ���**

// ��test���Bucket��Object 1.txt���ݸ��Ƶ�newtest���Bucket��Object 1.txt
$s->copyObject("test", "1.txt", "newtest", "1.txt");

// ��test���Bucket��Object 1.txt���ݸ��Ƶ�newtest���Bucket��Object 1.txt��������Object��������������ʱ��Ϊ10s��Content-TypeΪtext/plain
$s->copyObject("test", "1.txt", "newtest", "1.txt", array('expires' => '10s'), array('Content-Type' => 'text/plain'));

**����һ�������ܹ����ʵ�url**

// Ϊ˽��Bucket test�е�Object 1.txt����һ���ܹ���������GET������ʱ���ʵ�URL����URL����ʱ��Ϊ600s
$s->getTempUrl("test", "1.txt", "GET", 600);

// Ϊtest���Bucket�е�Object 1.txt����һ������CDN���ʵ�URL
$s->getCdnUrl("test", "1.txt");

**����ģʽ**

// ��������ģʽ���������ʱ�򷽱㶨λ���⣬����Ϊtrue�����������ʱ����׳��쳣������дһ��warning��Ϣ����־��
$s->setExceptions(true);
?>