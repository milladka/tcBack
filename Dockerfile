FROM php:7.2-cli
COPY . /usr/src/tcpay
WORKDIR /usr/src/tcpay
CMD [ "php", "-S", "0.0.0.0:80", "./index.php" ]