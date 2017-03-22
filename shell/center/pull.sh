#!/bin/bash
#
# Program: 从各个数据节点拉取数据，并入库
# Author: Peter
# Date: 2015/12/18
#

base_path=$(cd `dirname $0`; pwd)

conf_name=${base_path}"/sh.conf"

data_path=${base_path}"/data/"

#加载配置
load_config()
{
	if [ ! -e $conf_name ] || [ ! -f $conf_name ]
	then
		my_echo "加载配置文件失败: ${conf_name}"
		exit -1
	else
		source $conf_name
	fi
}

#节点连接测试
test_nodes()
{
	for node in ${servers[@]}
	do
		node=($(echo $node|tr "/" " "))
		$(wget ftp://${node[1]}:${node[2]}@${node[0]}/ --quiet --spider -T1 -t1)
		if [ $? != 0 ]
		then
			my_echo "连接失败: ${node[0]}"
			exit -1
		fi
	done
}

#拉取数据
pull()
{
	for node in ${servers[@]}
	do
		node=($(echo $node|tr "/" " "))
		
		data_dir=${data_path}${node[0]}
		if [ ! -e $data_dir ] || [ ! -d $data_dir ]
		then
			$(mkdir $data_dir)
		fi
		
		file_name=$(date -d"-10 minute" +%y%m%d%H%M | sed '$s/.$/'*'/')
		$(wget ftp://${node[1]}:${node[2]}@${node[0]}/${file_name}.log -P ${data_dir} -c --quiet -T1 -t1)
		if [ $? != 0 ]
		then
			my_echo "从${node[0]}拉取数据失败"
			exit -1
		fi
	done
}

#数据入库
write()
{
	/usr/local/php/bin/php ${base_path}/storage.php
	if [ $? != 0 ]
	then
		my_echo "数据入库失败"
		exit -1
	fi
}

#带时间的输出
my_echo()
{
	echo `date '+%Y-%m-%d %H:%M:%S'` $1
}


main()
{
	load_config
	test_nodes
	pull
	write
}

main

exit 0

