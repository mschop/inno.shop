FROM ubuntu:latest

ARG USER_ID
ARG GROUP_ID

RUN addgroup --gid $GROUP_ID app
RUN adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID app

RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update && apt-get -y upgrade && apt-get -y --no-install-recommends install apt-utils \
    && apt-get -y --no-install-recommends install python3 python3-pip \
    && pip3 install mkdocs mkdocs-material

USER app
