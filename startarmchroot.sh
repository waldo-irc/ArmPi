#!/bin/bash
echo '[*] Checking for required stuff before continuing.'
file=$(which qemu-arm-static)
if [ ! -f $file ]; then
  sudo apt-get install qemu-arm-static
fi
file=$(which kpartx)
if [ ! -f $file ]; then
  sudo apt-get install kpartx
fi

if [ ! -f arm/ArmPIv*.img ]; then
  echo "[X] Can't continue without an ArmPI image in the arm folder."
  exit 0
fi

echo '[1.] Mounting IMG!'
if ! mount | grep --quiet /dev/mapper/loop0p2; then
  sudo kpartx -a -v arm/ArmPIv*.img
  sudo mount /dev/mapper/loop0p2 /mnt/temp
fi

echo '[2.] Preparing environment!'
if [ ! -f /mnt/temp/usr/bin/qemu-arm-static ]; then
  sudo cp /usr/bin/qemu-arm-static /mnt/temp/usr/bin
fi

echo '[3.] Checking mounts!'
checkmount () {
  if ! mount | grep --quiet $1; then
    sudo mount -o bind /dev $1
  fi
}

checkmount /mnt/temp/dev
checkmount /mnt/temp/proc
checkmount /mnt/temp/sys

echo '[4.] Setting up proc!'
if [ ! -f /proc/sys/fs/binfmt_misc/register ]; then
  echo ':arm:M::\x7fELF\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x02\x00\x28\x00:\xff\xff\xff\xff\xff\xff\xff\x00\xff\xff\xff\xff\xff\xff\xff\xff\xfe\xff\xff\xff:/usr/bin/qemu-arm-static:' > /proc/sys/fs/binfmt_misc/register
fi

echo '[5.] Blast off!'
sudo chroot /mnt/temp
