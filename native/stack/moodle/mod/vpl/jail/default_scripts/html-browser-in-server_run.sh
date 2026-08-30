#!/bin/bash
# This file is part of VPL for Moodle - http://vpl.dis.ulpgc.es/
# Script for running HTML using the PHP Built-in web server
# Copyright (C) 2025 onwards Juan Carlos Rodríguez-del-Pino
# License http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
# Author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>

# @vpl_script_description Using browser in execution server using GUI mode

# load common script
source common_script.sh

# Detect browser in Linux
check_program firefox-esr firefox chromium-browser chromium x-www-browser
if [ "$1" == "version" ] ; then
	get_program_version --version
fi

compile_typescript
compile_scss
cat > vpl_wexecution << "EOF"
#!/bin/bash
source common_script.sh
get_first_source_file html
check_program firefox-esr firefox chromium-browser chromium x-www-browser
"$PROGRAM" "file://$(pwd)/$FIRST_SOURCE_FILE"
sleep 10000
EOF

chmod +x vpl_wexecution
