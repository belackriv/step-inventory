# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.require_version ">=1.7"
# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  	config.vm.box = "ubuntu/xenial64"

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  	config.vm.network "forwarded_port", guest: 80, host: 8080
    config.vm.network "forwarded_port", guest: 443, host: 8443

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  # config.vm.network "private_network", ip: "192.168.33.10"
  	config.vm.network "private_network", type: "dhcp"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  	config.vm.synced_folder ".", "/vagrant", disabled: true

    config.vm.synced_folder ".", "/var/www/step-inventory",
      type: "smb",
      owner: "ubuntu",
      group: "www-data",
      mount_options: ["mfsymlinks,dir_mode=0774,file_mode=0774"]#"vers=3.02"],
      #type: "nfs",
      #mount_options: ['rw', 'vers=3', 'tcp', 'fsc' ,'actimeo=2']

    #config.winnfsd.uid = 1000
    #config.winnfsd.gid = 33


  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  	config.vm.provider "virtualbox" do |vb|
  #   # Display the VirtualBox GUI when booting the machine
    	vb.gui = false
  #
  #   # Customize the amount of memory on the VM:
  		vb.memory = "2048"
  		vb.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/var/www/step-inventory", "1"]
  	end
  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Define a Vagrant Push strategy for pushing to Atlas. Other push strategies
  # such as FTP and Heroku are also available. See the documentation at
  # https://docs.vagrantup.com/v2/push/atlas.html for more information.
  # config.push.define "atlas" do |push|
  #   push.app = "YOUR_ATLAS_USERNAME/YOUR_APPLICATION_NAME"
  # end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.

  #redis stuff at https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-redis-on-ubuntu-16-04
  #mail stuff at https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-postfix-as-a-send-only-smtp-server-on-ubuntu-16-04
 	config.vm.provision "shell", inline: <<-SHELL
 		mkdir /var/www/step-inventory
 		cd /var/www/step-inventory
		sudo apt-get install software-properties-common
		sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
		sudo add-apt-repository 'deb [arch=amd64,i386,ppc64el] http://ftp.utexas.edu/mariadb/repo/10.1/ubuntu xenial main'
		sudo apt-get update
		sudo apt-get install -y apache2
		sudo a2enmod rewrite
		sudo cp ./001-step-inventory.conf /etc/apache2/sites-available/001-step-inventory.conf
		sudo rm /etc/apache2/sites-enabled/000-default.conf
		pushd /etc/apache2/sites-enabled
		sudo ln -s ../sites-available/001-step-inventory.conf ./001-step-inventory.conf
		popd
		sudo apt-get install -y php
		sudo apt-get install -y php-xml
		sudo apt-get install -y php-mysql
    sudo apt-get install -y php-bcmath
    sudo apt-get install -y php-curl
    sudo apt-get install -y php7.0-zip
    sudo apt-get install -y php-gd
    sudo apt-get install -y php-fpm
    sudo apt-get install -y php-redis
    sudo apt-get install -y php-mbstring
    sudo apt-get install -y libapache2-mod-fastcgi
    sudo a2enmod actions fastcgi alias
    sudo service apache2 restart
	SHELL

	#do this after
	# sudo apt install -y mariadb-server
	# sudo mysql -uroot -pvagrant < create_db_user.sql
	# sudo mysql -uroot -pvagrant 'step-inventory' < db_dump.sql
  # set session.serialize_handler = php_serialize in php.ini
end
