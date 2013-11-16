include_recipe "apt"
include_recipe "build-essential"
include_recipe "networking_basic"
include_recipe "apache2"
include_recipe "apache2::mod_php5"
include_recipe "apache2::mod_rewrite"
include_recipe "apache2::mod_deflate"
include_recipe "apache2::mod_headers"
include_recipe "postgresql::client"
include_recipe "postgresql::server"
include_recipe "mysql::server"
include_recipe "vagrant_main::custom_php"
include_recipe "elasticsearch"
include_recipe "ant"
include_recipe "memcached"
include_recipe "git"
include_recipe "nodejs"
include_recipe "nodejs::npm"

# Install mysql gem
gem_package "mysql" do
  action :install
end

execute "install_php_pg_admin" do
  command "sudo DEBIAN_FRONTEND=noninteractive apt-get install phpmyadmin -y -q"
  ignore_failure false
end

# Install pg gem
gem_package "pg" do
  action :install
end

execute "install_php_pg_admin" do
  command "sudo apt-get install phppgadmin -y -q"
  ignore_failure false
end

execute "setup_postgres" do
  command "sudo -u postgres psql -c \"ALTER USER postgres WITH PASSWORD 'postgres';\""
  ignore_failure false
end

execute "setup_phppgadmin" do
  command "sudo sed -i \"s/[']extra_login_security[']\] = true/'extra_login_security'\] = false/g\" /etc/phppgadmin/config.inc.php"
  ignore_failure false
end

execute "setup_phppgadmin_apache" do
  command "sudo sed -i \"s/# allow from all/allow from all/g\" /etc/phppgadmin/apache.conf"
  ignore_failure false
end

execute "setup_phpmyadmin_apache" do
  command "sudo ln -s /etc/phpmyadmin/apache.conf /etc/apache2/conf.d/phpmyadmin"
  ignore_failure false
end

execute "restart_apache" do
  command "sudo service apache2 reload"
  ignore_failure false
end

ruby_block "Create Mysql database + execute grants" do
  block do
    require 'rubygems'
    Gem.clear_paths
    require 'mysql'
    m = Mysql.new("localhost", "root", "root")
    m.query("CREATE DATABASE IF NOT EXISTS mediamine CHARACTER SET utf8")
    m.query("grant all on `app`.* to 'root'@'10.0.2.2' identified by ''")
    m.reload
  end
end

ruby_block "Create Postgresql database + execute grants" do
  block do
    require 'rubygems'
    Gem.clear_paths
    require 'pg'
    conn = PGconn.connect("localhost", 5432, "", "", "", "postgres", "postgres")
    res = conn.exec("CREATE DATABASE mediamine WITH OWNER postgres TEMPLATE template0 ENCODING 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';")
  end
end

execute "install_graphviz" do
  command "sudo apt-get install graphviz -y -q"
  ignore_failure false
end

# Initialize web app
web_app "default" do
    template "default.conf.erb"
    server_name "localhost"
    server_aliases [node['fqdn'], "localhost"]
    docroot "/vagrant/public"
end
