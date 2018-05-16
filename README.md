# ArmPIv1 
(Also known as the ArmPI Revision T (ArmPIT)

### Raspbian IMG for ARM Reverse Engineering

I had a lot of CTFs where I needed to reverse engineer ARM binaries lately and I decided it was finally time to setup a way to do actually handle it, and thus the ArmPI was born.  The .img file was created using Raspbian LITE and a Raspberry PI zero - its also been tested on a Raspberry Pi2 and in Qemu (though it's a little slow in Qemu because of the 256mb ram limit for the kernel).

In  this repository you will find the HTML files and scripts used to create the ArmPI as well as the configuration files used to set it up in Qemu.  The config was used in Windows but it should work just fine in Linux, though this hasn't been tested yet.


