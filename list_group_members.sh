#!/bin/bash

for userid in `awk -F: '{print $1}' /etc/passwd`; do id $userid; done | grep "web" | tr ")" " " | tr "(" " " | awk '{print $2}' 