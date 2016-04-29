# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "debian/jessie64"
  config.vm.network "private_network", ip: "192.168.33.10"
  config.vm.synced_folder ".", "/vagrant"
  #config.vm.synced_folder ".", "/vagrant", type: "nfs"
  #config.vm.synced_folder ".", "/vagrant", type: "rsync", rsync__exclude: [".git/", "app/config/parameters.yml", "build/", "phpunit.xml", "var/bootstrap.php.cache", "var/cache/*", "var/logs/*", "var/sessions/*", "vendor/", "web/bundles/"]
  config.vm.provision "shell", path: "bin/provision"
  config.vm.provider "virtualbox" do |v|
    v.memory = 1024
    v.cpus = 1
  end
  config.ssh.forward_agent = true
end
