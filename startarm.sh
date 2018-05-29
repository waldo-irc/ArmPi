#!/bin/bash
if [[ $1 == "-q" ]]; then
qemu-system-arm -m 2048M -M vexpress-a15 -cpu cortex-a15 -kernel arm/kernel-qemu-4.4.1-vexpress -no-reboot -dtb arm/vexpress-v2p-ca15_a7.dtb -sd arm/ArmPIv*.img -serial stdio -append "root=/dev/mmcblk0p2 rw rootfstype=ext4 console=ttyAMA0,15200 loglevel=8" -net user,hostfwd=tcp::80-:80,hostfwd=tcp::443-:443,hostfwd=tcp::2222-:22,hostfwd=tcp::8088-:8088,hostfwd=tcp::10100-:10100,hostfwd=tcp::9090-:9090,hostfwd=tcp::23000-:23000  -net nic -display none
else
qemu-system-arm -m 2048M -M vexpress-a15 -cpu cortex-a15 -kernel arm/kernel-qemu-4.4.1-vexpress -no-reboot -dtb arm/vexpress-v2p-ca15_a7.dtb -sd arm/ArmPIv*.img -serial stdio -append "root=/dev/mmcblk0p2 rw rootfstype=ext4 console=ttyAMA0,15200 loglevel=8" -net user,hostfwd=tcp::80-:80,hostfwd=tcp::443-:443,hostfwd=tcp::2222-:22,hostfwd=tcp::8088-:8088,hostfwd=tcp::10100-:10100,hostfwd=tcp::9090-:9090,hostfwd=tcp::23000-:23000  -net nic
fi
