#数据节点初始化（需有root权限）

# 安装vsftpd
yum -y install vsftpd
chkconfig vsftpd on

# 添加用户
useradd -g ftp -s /sbin/nologin -d /var/ftp/datalog -M datalog
#pass.txt密码生成方式：openssl passwd -1
chpasswd -e < pass.txt

# 创建ftp访问目录
mkdir -p /var/ftp/datalog
chown datalog.ftp /var/ftp/datalog

# 配置vsftpd
conf_file="/etc/vsftpd/vsftpd.conf"
if [ ! -f /etc/vsftpd/vsftpd.conf.sample ];then
	cp ${conf_file} /etc/vsftpd/vsftpd.conf.sample
fi
echo 'anonymous_enable=NO' > ${conf_file} 
echo 'local_enable=YES' >> ${conf_file}
echo 'write_enable=NO' >> ${conf_file}
echo 'local_umask=022' >> ${conf_file}
echo 'local_root=/var/ftp/datalog' >> ${conf_file}
echo 'anon_upload_enable=NO' >> ${conf_file}
echo 'anon_mkdir_write_enable=NO' >> ${conf_file}
echo 'xferlog_enable=YES' >> ${conf_file}
echo 'connect_from_port_20=YES' >> ${conf_file}
echo 'xferlog_file=/var/log/xferlog' >> ${conf_file}
echo 'xferlog_std_format=YES' >> ${conf_file}
echo 'dual_log_enable=YES' >> ${conf_file}
echo 'vsftpd_log_file=/var/log/vsftpd.log' >> ${conf_file}
echo 'idle_session_timeout=180' >> ${conf_file}
echo 'data_connection_timeout=120' >> ${conf_file}
echo 'ascii_upload_enable=NO' >> ${conf_file}
echo 'ascii_download_enable=NO' >> ${conf_file}
echo 'chroot_local_user=YES' >> ${conf_file}
echo 'chroot_list_enable=NO' >> ${conf_file}
echo 'listen=YES' >> ${conf_file}
echo 'pam_service_name=vsftpd' >> ${conf_file}
echo 'userlist_enable=YES' >> ${conf_file}
echo 'tcp_wrappers=YES' >> ${conf_file}

# CentOS 7
# vi /etc/sysconfig/iptables-config
# 修改一行：IPTABLES_MODULES="ip_nat_ftp ip_conntrack_ftp"

# 启动vsftpd
service vsftpd start
