# ArmPIv1 (Beta)
Also known as the ArmPI Revision T (ArmPIT)

### Raspbian IMG for ARM Reverse Engineering

I had a lot of CTFs where I needed to reverse engineer ARM binaries lately and I decided it was finally time to setup a way to do actually handle it, and thus the ArmPI was born.  The .img file was created using Raspbian LITE and a Raspberry PI zero - its also been tested on a Raspberry Pi2 and in Qemu (though it's a little slow in Qemu because of the 256mb ram limit for the kernel).

In  this repository you will find the HTML files and scripts used to create the ArmPI as well as the configuration files used to set it up in Qemu.  The config was used in Windows but it should work just fine in Linux, though this hasn't been tested yet.

*Disclaimer - Currently this is a rough draft of what will hopefully become a more robust final product.  As a result, keep in mind there are a few security risks present here as well as a lot of this code is just to get this running at first and will be re-worked.  Keep in mind, as a reversing tool you will also potentially be running malicious ARM binaries as an additional risk, the idea was as a raspberry pi/.img file ran in QEMU you could destroy and re-create the environment as needed with little risk of compromise to any important systems.  

## Reversing tools

Most of the common reversing tools have been installed.  GDB Peda will soon be replaced by GEF as Peda does not currently understand ARM.

* GDB and GDBServer
* Radare2
* ltrace/strace
* MSF for MSVenom payload generation
* Nasm tools for manual shellcode creation
* Objdump
* As well as documentation to assist in Arm RE

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


## TODO
* Display which binaries are running on the page alongside PID.
* [Ongoing] Improve UI.
* Resolve Radare GUI issues (Graph capabilities etc.)

