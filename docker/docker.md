```text
docker stop php72
docker rm php72
docker run --name php72 -d -v /Users/zmm/workspace:/users/zmm/workspace --volume=/usr/local/docker/php72/logs:/usr/local/var/log --volume=/usr/local/docker/php72/etc:/usr/local/etc -p 9002:9000 arodax/php7.2-fpm
```
