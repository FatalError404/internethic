<?php /* #?ini charset="utf-8"?
# Configuration file for audit http link edited in eZ Publish Backoffice
#
# Script will detect all URL edited in backoffice, that contain http term, below requirement
# In modify mode the script reset these url with https (see NewTerm)
# If you prefer you can directly set the expected parameters on script run (see help options)

[HTTPAuditSettings]
Term=http
# Modify this parameter with your domain
Domain=<your domain>
NewTerm=https


*/ ?>