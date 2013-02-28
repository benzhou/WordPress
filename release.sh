#!/bin/bash

echo "Go to directory"
cd ~/mysites/WordPress
echo "Staging all changes"
git add .
echo "Committing current changes"
git commit -a -m "Auto Release commit"
echo "Pushing current committed changes"
git push
echo "git push Completed!..."
echo
echo "Next: connect AWS EC2 server"
expect -c "spawn ssh ubuntu@benzhouonline.com 
expect \"(yes/no)?\"
send \"yes\r\"
expect \"$\"
send \"cd ~/sites/wp/WordPress\r\"
expect \"$\"
send \"git pull\r\"
expect \"$\"
send \"exit\r\"
"


