# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.hostname = "vm-testing-neo4j"

  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box" # Ubuntu precise 64 VirtualBox

  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

  config.vm.network :private_network, ip: "33.33.33.33"
  config.vm.network "forwarded_port", guest: 7474, host: 7474 # neo4j

  nfs_setting = RUBY_PLATFORM =~ /darwin/ || RUBY_PLATFORM =~ /linux/
  config.vm.synced_folder "./shared/", "/var/shared", id: "vagrant-root", :nfs => nfs_setting

  config.vm.provision :chef_solo do |chef|
    chef.log_level = :info
    # chef.log_level = :debug

    chef.cookbooks_path = ["vagrant/cookbooks"]

    chef.add_recipe "apt"
    chef.add_recipe "mc"
    chef.add_recipe "curl"
    chef.add_recipe "java"
    chef.add_recipe "php"
    chef.add_recipe "php::module_curl"
    chef.add_recipe "apache2"
    chef.add_recipe "apache2::mod_php5"
    chef.add_recipe "neo4j-server::tarball"

    chef.json = {
        :java => {
          :install_flavor => 'openjdk',
          :jdk_version => '7',
          :java_home => '/usr/lib/jvm/java-7-openjdk-amd64'
        },
        
        :neo4j => {
          :server => {
            :version => "2.0.1",
            :remote_shell => {
              :port => 1337
            }
          }
        },

        :php => {
          :directives => {
            "error_log" => "/vagrant/logs/php.log",
            "display_errors" => "On"
          }
        },

        :apache => {
          :default_site_enabled => true,
          :docroot_dir => "/vagrant/shared/php/www",
          :log_dir => "/vagrant/logs"
        }
    }

    config.vm.provision "shell", inline: "rm -f /vagrant/neo4j/server && ln -s /usr/local/neo4j-server /vagrant/neo4j/server"
    config.vm.provision "shell", inline: "rm -f /vagrant/neo4j/data && ln -s /var/lib/neo4j-server/data/ /vagrant/neo4j/data"

    config.vm.provision "shell", inline: "curl --version | head -1"
    config.vm.provision "shell", inline: "java -version 2>&1 | grep version"
    config.vm.provision "shell", inline: "cat /usr/local/neo4j-server/README.txt | head -1"
    config.vm.provision "shell", inline: "php -version | head -1"
  end

  config.vm.provision "shell", inline: "echo VM booted successfully!"
end