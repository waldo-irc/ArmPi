# ArmPIv1 (Alpha)
Also known as the ArmPI Revision T (ArmPIT)

### Raspbian IMG for ARM Reverse Engineering

I had a lot of CTFs where I needed to reverse engineer ARM binaries lately and I decided it was finally time to setup a way to actually handle it, and thus the ArmPI was born.  The .img file was created using Raspbian LITE and a Raspberry PI zero - its also been tested in Qemu (though it's a little slow in Qemu because of the 256mb ram limit for the kernel).  It will probably work on an RPI2 or RPI3 though it hasn't been tested...

-- UPDATE: No qemu accelerator exists to speed up ARM unfortunately but I have been able to replace the kernel with an express kernel that can take more than 256mb of ram.  You can now use the express kernel to get more ram, if that fails you should still be able to use the tested versatile.ptb too just with the less ram.

In  this repository you will find the HTML files and scripts used to create the ArmPI as well as the configuration files used to set it up in Qemu.  The qemu image has been tested in Windows 10, Kali Linux, and OSX 10.13.4.

*Disclaimer - Currently this is a rough draft of what will hopefully become a more robust final product.  As a result, keep in mind there are a few security risks present here as well as a lot of this code is just to get this running at first and will be re-worked.  As a reversing tool you will also potentially be running malicious ARM binaries as an additional risk, the idea was as a raspberry pi/.img file ran in QEMU you could destroy and re-create the environment as needed with little risk of compromise to any important systems.  

## Reversing tools

Most of the common reversing tools have been installed.  GDB Peda will soon be replaced by GEF as Peda does not currently understand ARM.

* GDB and GDBServer
* Radare2
* ltrace/strace
* MSF for MSVenom payload generation
* Nasm tools for manual shellcode creation
* Objdump
* As well as documentation to assist in Arm RE

## Installing in QEMU (Windows)
Setup on windows is quite easy.

1. Install QEMU: https://www.qemu.org/download/#windows
2. a. Either put the executables from Program Files/qemu in your path or
   b. Store the .img file in the arm folder and move the start-qemu-arm.bat and arm folder to the qemu directory, EX:Program Files/qemu, and create a shortcut to the bat file on your desktop.
3. double click the .bat and watch magic fly -
4. https://127.0.0.1 for the web portal. 

-- Ports open in QEMU redirection are 2222 for ssh, 80 and 443 for web, and 8088 for the web shell.  If any of these ports are in use on the host machine you wont be able to access the ArmPI.

## Installing in QEMU (Linux) 
Also easy.

1. Sudo apt-get install qemu qemu-system-arm
2. Move ARM folder from the repo to your Desktop and CD to Desktop
3. Move the .img file to the ARM folder.
4. bash startarm.sh

## Installing in QEMU (OSX)
Easy with brew.  Will not cover how to install brew.

1. brew install qemu
2. Move ARM folder from the repo to your Desktop and CD to Desktop
3. Move the .img file to the ARM folder.
4. bash startarm.sh

## Screenshots

![Alt text](/qemu.png?raw=true "Web GUI")
**Qemu**

![Alt text](/gui.png?raw=true "Web GUI")
**Radare GUI**

![Alt text](/dbg.png?raw=true "Web GUI")
**Radare Debugger**

![Alt text](/shell.png?raw=true "Web GUI")
**WebShell with GDB**

## Usage
**IP**: 192.168.4.1

**SSID**: ArmPI
**Password**: armpilocal

**Web User**: root
**Web Password**: toor

**SSH User**: pi
**SSH Password**: raspberry

##### Qemu port redirection is done for a few ports in the batch file if you go the emulator route.
**SSH**: 127.0.0.1:2222
**WebPortal**: 127.0.0.1:80 AND 443
**NodeWebShell**: 127.0.0.1:8088

### Extra Feature -*-
Some extra things were done to allow for things such as internet sharing on the pi.  You are able to ssh into the pi using rndis and the name armpi.local.  You will then be able to use internet sharing on the RNDIS gadget to do things such as update the pi or utilize it without using the access point.  A full guide for this will be forthcoming.

For an RPI zero this simply requires plugging a USB into your PC into the second micro usb slot and have an RNDIS driver installed on your machine to be able to see it on the network.

## Practice Binary
A practice web server binary from https://github.com/saelo/armpwn has been included for you to practice on.  All you need to do is hit "Begin the Challenge" and a it will start the process and attach it to radare (and gdbserver if you leave the setting enabled).

## TODO
* Display which binaries are running on the page alongside PID.
* [Ongoing] Improve UI.
* Resolve Radare GUI issues (Graph capabilities etc.)
* Allow webshell user to gdbserver so SSH is not necessary.
* Add button to allow starting gdb --multi server for remote debugging of any binaries.
* Replace GDB Peda with GEF 
