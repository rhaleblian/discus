import pyffi.formats.nif

paths = []
#paths.append('d:/programs/niftools/doomsicle/meshes/weapons/iron/1stpersondoomsicle.nif')
#paths.append('c:/users/rhaleblian/documents/maya/projects/default/perfect.nif')
#paths.append('d:/programs/niftools/skyrim/meshes/armor/ebony/m/boots_1.nif')
paths.append('D:/programs/niftools/meshes/armor/ebony/m/boots_1.nif')

for path in paths:
    fp = open(path, 'rb')
    data = pyffi.formats.nif.NifFormat.Data()
    #data.inspect(fp)
    #print path
    #print hex(data.version), hex(data.user_version)
    data.read(fp)
    fp.close()