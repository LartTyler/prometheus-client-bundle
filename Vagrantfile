# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
	config.vm.box = "ubuntu/bionic64"

	config.vm.provider "virtualbox" do |vb|
		vb.memory = "2048"
	end

	config.vm.provision "shell", inline: <<-SHELL
		apt-get update -y

		add-apt-repository -y ppa:ondrej/php

		apt-get install -y build-essential software-properties-common nodejs php7.3-common php7.3-cli php7.3-mysqlnd \
			php7.3-curl php7.3-zip php7.3-mbstring php7.3-xml php7.3-xdebug
		apt-get install -y composer

		if grep -Fqvx "xdebug.remote_enable" /etc/php/7.3/mods-available/xdebug.ini; then
			echo "xdebug.remote_enable = on" >> /etc/php/7.3/mods-available/xdebug.ini
			echo "xdebug.remote_connect_back = on" >> /etc/php/7.3/mods-available/xdebug.ini
			echo "xdebug.idekey = application" >> /etc/php/7.3/mods-available/xdebug.ini
			echo "xdebug.remote_autostart = off" >> /etc/php/7.3/mods-available/xdebug.ini
			echo "xdebug.remote_host = 10.0.2.2" >> /etc/php/7.3/mods-available/xdebug.ini
		fi
	SHELL

	config.vm.provision "shell", privileged: false, inline: <<-SHELL
		cd /vagrant

		composer install
	SHELL
end