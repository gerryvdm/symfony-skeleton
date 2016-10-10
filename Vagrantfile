Vagrant.configure("2") do |config|
    config.vm.box = "debian/jessie64"
    config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.synced_folder ".", "/vagrant", type: "nfs"
    config.vm.provision "shell", path: "bin/provision"
    config.vm.provider "virtualbox" do |vb|
        vb.memory = 1024
        vb.cpus = 1
    end
    config.ssh.forward_agent = true
end
