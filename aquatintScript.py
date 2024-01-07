#!/usr/bin/env python
# coding: utf-8

import sys
filename = sys.argv[1]
greycut = float(sys.argv[2])
temperature = float(sys.argv[3])
totalsweeps = int(sys.argv[4])

import numpy as np
import matplotlib as mpl
import matplotlib.pyplot as plt
import scipy.misc
import json

import imageio

def write_to_json(filename,string):
    f = open(filename, 'w')
    f.write(string)
    f.close()
    return True


status_dict = {"origin":False,"greycut":False,"temperature":False,"sweeps":dict(),"finished":0,"total":3+totalsweeps}
for i in range(0,totalsweeps):
    status_dict['sweeps']["sweep"+str(i)] = False

write_to_json(filename.split('.')[-2]+'-status.json',json.dumps(status_dict))
print(json.dumps(status_dict))

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
#cb = plt.colorbar(hsimage)
plt.savefig(filename.split('.')[-2]+'-origin.jpg',dpi=300)

status_dict["origin"] = True
status_dict['finished'] += 1
write_to_json(filename.split('.')[-2]+'-status.json',json.dumps(status_dict))
print(json.dumps(status_dict))

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
plt.savefig(filename.split('.')[-2]+'-greycut.jpg',dpi=300)
sth=1

status_dict['greycut'] = True
status_dict['finished'] += 1
write_to_json(filename.split('.')[-2]+'-status.json',json.dumps(status_dict))
print(json.dumps(status_dict))

########################
Nx=Niy
Ny=Nix
nsites=Nx*Ny
beta=1/temperature

status_dict['temperature'] = True
status_dict['finished'] += 1
write_to_json(filename.split('.')[-2]+'-status.json',json.dumps(status_dict))
print(json.dumps(status_dict))

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
    plt.savefig(filename.split('.')[-2]+'-sweep'+str(nsweeps)+'.jpg',dpi=300)
    status_dict['sweeps']['sweep'+str(nsweeps)] = True
    status_dict['finished'] += 1
    write_to_json(filename.split('.')[-2]+'-status.json',json.dumps(status_dict))
    print(json.dumps(status_dict))
    pass

hsimage=plt.imshow(dsq,cmap='Greys',aspect=1,interpolation='none')
#cb.remove()
plt.axis('off')
plt.savefig(filename.split('.')[-2]+'-aquatint.jpg',dpi=300)
plt.close()



