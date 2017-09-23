#!/usr/bin/env bash
echo "Adding api.counters.loc host to the /etc/hosts ..."
sudo -- sh -c "echo '127.0.0.1 api.counters.loc' >> /etc/hosts" 

sh run.sh
