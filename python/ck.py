""" Musings re: Creation Kit and PyFFI.
req: win32. python 3?
"""
import os
import pyffi.formats.nif

data_root = 'd:/programs/steam/steamapps/common/skyrim/data/unpack'
paths = []
paths.append(os.path.join(data_root, 'meshes/armor/ebony/m/boots_1.nif'))
#paths.append('d:/programs/niftools/doomsicle/meshes/weapons/iron/1stpersondoomsicle.nif')
#paths.append('c:/users/rhaleblian/documents/maya/projects/default/perfect.nif')

for path in paths:
    fp = open(path, 'rb')
    data = pyffi.formats.nif.NifFormat.Data()
    #data.inspect(fp)
    #print path
    #print hex(data.version), hex(data.user_version)
    data.read(fp)
    fp.close()