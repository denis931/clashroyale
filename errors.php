<?php
$status = $_SERVER['REDIRECT_STATUS'];
$codes = array(
       400 => array('400 ������ ������', '������ �� ����� ���� ��������� ��-�� �������������� ������.'),
       403 => array('403 ���������', '������ ���������� � ���������� ������ ������� �� ����� �����.'),
       404 => array('404 �� �������', '������������� �������� �� ������� �� ����� �������.<br><br><a href=http://wm-scripts.ru>������� �����</a>'),
       405 => array('405 ����� �� �����������', '��������� � ������� ����� �� ����������� ��� ��������� �������.'),
       408 => array('408 ����� �������� �������', '��� ������� �� �������� ���������� �� ������ �� ���������� �����.'),
       500 => array('500 ���������� ������ �������', '������ �� ����� ���� ��������� ��-�� ���������� ������ �������.'),
       502 => array('502 ������ ����', '������ ������� ������������ ����� ��� ������� �������� �������.'),
       504 => array('504 ������� ����� �������� �����', '����������� ������ �� ������� �� ������������� �����.'),
);
 
$title = $codes[$status][0];
$message = $codes[$status][1];
if ($title == false || strlen($status) != 3) {
       $message = '��� ������ HTTP �� ����������.';
}
 
echo '<h1>��������! ���������� ������ �� ����� �������� '.$title.'!</h1>
<p>'.$message.'</p>';