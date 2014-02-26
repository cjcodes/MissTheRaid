rm -rf app/cache/*
rm -rf app/logs/*

APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data' | grep -v root | head -1 | cut -d\  -f1`
chmod -R +a "$APACHEUSER allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs app/Resources/public
chmod -R +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs app/Resources/public
