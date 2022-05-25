#!/usr/bin/env python
# coding: utf-8

import sys
filename = sys.argv[1]

import numpy as np
import matplotlib as mpl
import matplotlib.pyplot as plt
import scipy.misc
import imageio

#---
# Added these guys:
from IPython import get_ipython
from IPython.terminal.interactiveshell import TerminalInteractiveShell
shell = TerminalInteractiveShell.instance()

#moved this :
get_ipython().run_line_magic('matplotlib', 'inline')
#---

#provide the name of the file between quotes (like "eins.jpg")
#filename = "publiceinstein.jpg"

im2 = imageio.imread(filename)
Nix=im2.shape[0]
Niy=im2.shape[1]
grayimage=np.zeros([Nix,Niy])
for i in range(0,Nix):
        for j in range(0,Niy):
            blueComponent = im2[i][j][0]
            greenComponent = im2[i][j][1]
            redComponent = im2[i][j][2]
            grayValue = 0.07 * blueComponent + 0.72 * greenComponent + 0.21 * redComponent
            grayimage[i][j] = grayValue
            pass
dsqin=1-grayimage/255.0
hsimage=plt.imshow(dsqin,cmap='Greys',aspect=1,interpolation='none')
plt.colorbar(hsimage)
plt.show(hsimage)    

greycut=0.79

#################################

nan=np.ndarray.flatten(dsqin)
nsites=Nix*Niy
hhbw=np.zeros(nsites)
for jj in range(nsites):
            if nan[jj]<greycut: hhbw[jj]=-1
            else: hhbw[jj]=1
            pass  

dsq=np.reshape(hhbw,(Nix,Niy))
hsimage=plt.imshow(dsq,cmap='Greys',aspect=1,interpolation='none')
plt.colorbar(hsimage)
plt.show(hsimage)  

temperature=2.5
totalsweeps=2
sth=1
########################
Nx=Niy
Ny=Nix
nsites=Nx*Ny
beta=1/temperature

v=np.zeros(nsites)
sig=2*v-1   
sumen=0

for nsweeps in range(totalsweeps):
    for npick in range(nsites):
        xx=np.random.randint(Nx)
        yy=np.random.randint(Ny)
        sumsig=sig[((xx+1)%Nx)+Nx*yy]+sig[((xx-1)%Nx)+Nx*yy]+sig[xx+Nx*((yy+1)%Ny)]+sig[xx+Nx*((yy-1)%Ny)]
        local=sig[xx+Nx*yy]*(beta*sumsig+sth*hhbw[xx+Nx*yy])
        if local<=0:
            sig[xx+Nx*yy]*=(-1)
        else:
            pp=np.random.uniform(0,1)
            probflip=np.exp(-2*local)
            if pp<=probflip:
                sig[xx+Nx*yy]*=(-1)
        pass
    v=((sig+1)/2)
    dsq=np.reshape(v,(Ny,Nx))
    hsimage=plt.imshow(dsq,cmap='Greys',aspect=1,interpolation='none')
    plt.axis('off')
    plt.show(hsimage)
    pass

hsimage=plt.imshow(dsq,cmap='Greys',aspect=1,interpolation='none')
plt.axis('off')
#plt.savefig("mypict4.jpg",dpi=300)
plt.savefig(filename.split('.')[-2]+'-acq.jpg',dpi=300)
plt.show(hsimage)



