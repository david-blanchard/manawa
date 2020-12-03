FROM php:7.1-apache

# Avoid warnings by switching to noninteractive
ENV DEBIAN_FRONTEND=noninteractive

# This Dockerfile adds a non-root user with sudo access. Use the "remoteUser"
# property in devcontainer.json to use it. On Linux, the container user's GID/UIDs
# will be updated to match your local UID/GID (when using the dockerFile property).
# See https://aka.ms/vscode-remote/containers/non-root-user for details.
ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=$USER_UID

# Configure apt and install packages
RUN apt-get update && apt-get -y upgrade \
    && apt-get -y install --no-install-recommends apt-utils dialog 2>&1 \
    && apt-get -y install git openssh-client inetutils-tools inetutils-ping less iproute2 procps lsb-release zip unzip bc \
    curl gnupg locales zsh wget fonts-powerline \
    # linuxbrew-wrapper
    # set up locale
    && locale-gen fr_FR.UTF-8 \
    && yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && docker-php-ext-enable xdebug \
    # Create a non-root user to use if preferred - see https://aka.ms/vscode-remote/containers/non-root-user.
    && groupadd --gid $USER_GID $USERNAME \
    && useradd -s /bin/bash --uid $USER_UID --gid $USER_GID -m $USERNAME \
    && usermod -a -G www-data $USERNAME \
    # [Optional] Add sudo support for the non-root user
    && apt-get install -y sudo \
    && echo $USERNAME ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/$USERNAME\
    && chmod 0440 /etc/sudoers.d/$USERNAME \
    #
    # Clean up
    && apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*
COPY certs /certs
RUN unzip /certs/rootCA.zip -d /certs && unzip /certs/localhost.zip -d /certs 
COPY apache/manawa.conf apache/ssl-manawa.conf /etc/apache2/sites-available/
COPY php/php.ini /usr/local/etc/php/php.ini
COPY php/xdebug-settings.ini /usr/local/etc/php/conf.d/xdebug-settings.ini
RUN cat /usr/local/etc/php/conf.d/xdebug-settings.ini >> /usr/local/etc/php/conf.d/xdebug.ini && rm /usr/local/etc/php/conf.d/xdebug-settings.ini
RUN a2enmod rewrite && a2enmod ssl && a2ensite ssl-manawa

# Switch back to dialog for any ad-hoc use of apt-get
ENV DEBIAN_FRONTEND=dialog

# oh-my-zsh
# set home
ENV HOME /home/$USER_NAME

# generate locale for agnoster
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && /usr/sbin/locale-gen

# the user we're applying this too (otherwise it most likely install for root)
USER $USER_NAME

ENV TERM xterm

# Set the default shell to bash rather than sh
ENV SHELL /bin/zsh

# run the installation script  
RUN wget https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh -O - | zsh || true \
# install powerlevel10k
&& git clone https://github.com/romkatv/powerlevel10k.git ~/.oh-my-zsh/custom/themes/powerlevel10k \
&& cd $HOME && curl -fsSLO https://raw.githubusercontent.com/romkatv/dotfiles-public/master/.purepower

# zsh configuration
#ADD .zshrc $HOME 


#ENV PORT 80
ENTRYPOINT []
#CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && docker-php-entrypoint apache2-foreground
CMD docker-php-entrypoint apache2-foreground
