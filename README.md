# ArmPIv3 (Release)
Also known as the ArmPI Revision T (ArmPIT)

### Raspbian IMG for ARM Reverse Engineering

I had a lot of CTFs where I needed to reverse engineer ARM binaries lately and I decided it was finally time to setup a way to actually handle it, and thus the ArmPI was born.  The .img file was created using Raspbian LITE and a Raspberry PI zero.  For best performance install on an RPI Zero Wifi.  May work on the RPI3, will not work on RPI2 or RPI1 due to lack of wireless card and RNDIS gadget.

Relevant Qemu and Chroot Scripts are here as well for emulated runs.  Qemu is a bit slow but the Chroot runs great.  Working out a few kinks trying to get the webserver to run in the Chroot, works effectively for TUI RE though.

In  this repository you will find the HTML files and scripts used to create the ArmPI as well as the configuration files used to set it up in Qemu.  The qemu image has been tested in Windows 10, Kali Linux, and OSX 10.13.4.  The Chroot was made for and tested only on Kali Linux (thought it should work with any Debian/Ubuntu OS).  

## Reversing tools

Most of the common reversing tools have been installed.  GDB Peda will soon be replaced by GEF as Peda does not currently understand ARM.

* GDB and GDBServer
* GDB GEF Plugin
* Radare2
* ltrace/strace
* MSF for MSVenom payload generation
* Nasm tools for manual shellcode creation
* GCC
* Objdump
* Pwntools
* As well as documentation to assist in Arm RE
*More to come including ANGR ETC.

## Installing in QEMU (Windows)
Setup on windows is quite easy.

1. Install QEMU: https://www.qemu.org/download/#windows
2. a. Either put the executables from Program Files/qemu in your path or
   b. Store the .img file in the arm folder and move the start-qemu-arm.bat and arm folder to the qemu directory, EX:Program Files/qemu, and create a shortcut to the bat file on your desktop.
3. double click the .bat and watch magic fly -
4. https://127.0.0.1 for the web portal. 

## Installing in QEMU (Linux) 
Also easy.

1. Sudo apt-get install qemu qemu-system-arm
2. Move ARM folder from the repo to your Desktop and CD to Desktop
3. Move the .img file to the ARM folder.
4. bash startarm.sh from your Desktop.

## Installing in CHROOT (Linux) 
Easiest.

1. Move the ARM folder to your Desktop.
2. Download and move the .img file to your ARM folder.
3. Run the startarmchroot.sh script from your Desktop, it will do all the work!
4. Youll need to run this using sudo or as root to install required packages if not already installed.

## Installing in QEMU (OSX)
Easy with brew.  Will not cover how to install brew.

1. brew install qemu
2. Move ARM folder from the repo to your Desktop and CD to Desktop
3. Move the .img file to the ARM folder.
4. bash startarm.sh from your Desktop.

## Screenshots

![Alt text](/qemu.png?raw=true "Qemu")
**Qemu**

![Alt text](/chroot.png?raw=true "Chroot")
**Chroot**

![Alt text](/web.png?raw=true "Web")
**WebUI**

![Alt text](/web2.png?raw=true "Web")
**WebUI2**

![Alt text](/radare.png?raw=true "Qemu")
**Radare WebGUI**

## Usage
**IP**: 192.168.4.1

**SSID**: ArmPI
**Password**: armpilocal

**Web User**: root
**Web Password**: toor

**SSH User**: pi
**SSH Password**: raspberry

### Extra Feature -*-
Using RNDIS Gadget and RNDIS Driver you can ssh to armpi.local using port 2 on your raspberry PI zero.  Port 1 is for charging only.  This allows you access without having to turn your PI into an Access Point (and also means regular RPI Zeros work too).

## Practice Binary
A practice web server binary from https://github.com/saelo/armpwn has been included for you to practice on.  All you need to do is hit "Begin the Challenge" and it will start the process and attach it to radare (and gdbserver if you leave the setting enabled).

## TODO
* Fix GDBServer in home.php file debug call
* Start and Finish Wiki
* Create Cloud instances
* Now that web is complete add additional tools
* Remove USB Mount stuff (not required)
* LXC/Docker/Chroot universal alternative with increased performance.
* Chromebook?
