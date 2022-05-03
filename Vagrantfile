# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
  config.vm.define "ads_api" do |instance|
    config.vm.box = "bento/ubuntu-18.04"
    config.vm.network "forwarded_port", guest: 80, host: 8080, host_ip: "127.0.0.1"
    config.vm.synced_folder "./", "/app"
    config.vm.provision "shell", path: './vagrant/provision/init.sh'

    config.vm.provider 'virtualbox' do |vb|
      vb.cpus = 1
      vb.memory = 1024
      #Наименование машины в VirtualBox UI
      vb.name = "ads_api"
    end
  end
end
