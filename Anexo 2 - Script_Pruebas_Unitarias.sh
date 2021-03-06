#!/bin/bash
echo '#############################################################################################################'
echo '############################################ File ###########################################################' 
echo '#############################################################################################################'
/usr//bin/file putty.exe
echo '#############################################################################################################'
echo '############################################ String #########################################################' 
echo '#############################################################################################################'
/usr/bin/strings --all putty.exe
echo '#############################################################################################################'
echo '############################################ Stat ###########################################################' 
echo '#############################################################################################################'
/usr/bin/stat putty.exe
echo '#############################################################################################################'
echo '############################################ ClamAV #########################################################' 
echo '#############################################################################################################'
clamscan -r /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################ FprotAV ########################################################' 
echo '#############################################################################################################'
/usr/local/bin/fpscan /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################ ComodoAV #######################################################' 
echo '#############################################################################################################'
/opt/COMODO/cmdscan -v -s /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################ BitDefenderAV ##################################################' 
echo '#############################################################################################################'
bdscan /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Maldet (LMD) ##################################################' 
echo '#############################################################################################################'
maldet -a /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# SSMA ##########################################################' 
echo '#############################################################################################################'
python3 /opt/SSMA/ssma.py /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Mastiff #######################################################' 
echo '#############################################################################################################'
mas.py /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Peframe #######################################################' 
echo '#############################################################################################################'
peframe /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Pev - readpe ##################################################' 
echo '#############################################################################################################'
readpe /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Pev - readpe dir ##############################################' 
echo '#############################################################################################################'
readpe -d /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Pev - readpe fxi ##############################################' 
echo '#############################################################################################################'
readpe -i /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Pev - readpe fxe ##############################################' 
echo '#############################################################################################################'
readpe -e /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Pev - pehash ##################################################' 
echo '#############################################################################################################'
pehash -a /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Pev - pescan ##################################################' 
echo '#############################################################################################################'
pescan -v /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Pev - pestr ###################################################' 
echo '#############################################################################################################'
pestr -so /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Pev - pepack ##################################################' 
echo '#############################################################################################################'
pepack /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Pescanner #####################################################' 
echo '#############################################################################################################'
python /opt/sift-files/scripts/pescanner.py /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# PackerId ######################################################' 
echo '#############################################################################################################'
python /opt/sift-files/scripts/packerid.py /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# VirtusTotal ###################################################' 
echo '#############################################################################################################'
python /opt/VirusTotalApi/vt/vt.py -fs /home/upa/Descargas/putty.exe -v
echo '#############################################################################################################'
echo '############################################# TrId ##########################################################' 
echo '#############################################################################################################'
/opt/trid/trid /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################# Yara ##########################################################' 
echo '#############################################################################################################'
yara /opt/yara-3.7.1/rules/index1.yar /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################## Ssdeep #######################################################' 
echo '#############################################################################################################'
ssdeep /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################## Origami - Cop ################################################' 
echo '#############################################################################################################'
pdfcop /home/upa/Descargas/prueba_pdf.pdf
echo '#############################################################################################################'
echo '############################################## Origami - Metadata ###########################################' 
echo '#############################################################################################################'
pdfmetadata /home/upa/Descargas/prueba_pdf.pdf
echo '#############################################################################################################'
echo '############################################## PdfId ########################################################' 
echo '#############################################################################################################'
python /opt/pdftools/pdfid.py /home/upa/Descargas/prueba_pdf.pdf
echo '#############################################################################################################'
echo '############################################## Pdf-Parser ###################################################' 
echo '#############################################################################################################'
python /opt/pdftools/pdf-parser.py /home/upa/Descargas/prueba_pdf.pdf
echo '#############################################################################################################'
echo '############################################## PyOlescanner #################################################' 
echo '#############################################################################################################'
python /opt/pyOLEScanner/pyOLEScanner.py /home/upa/Descargas/prueba_doc.doc
echo '#############################################################################################################'
echo '############################################## OleId ########################################################' 
echo '#############################################################################################################'
python /usr/local/lib/python2.7/dist-packages/oletools/oleid.py /home/upa/Descargas/prueba_doc.doc
echo '#############################################################################################################'
echo '############################################## Olemeta ######################################################' 
echo '#############################################################################################################'
python /usr/local/lib/python2.7/dist-packages/oletools/olemeta.py /home/upa/Descargas/prueba_doc.doc
echo '#############################################################################################################'
echo '############################################## Oletimes #####################################################' 
echo '#############################################################################################################'
python /usr/local/lib/python2.7/dist-packages/oletools/oletimes.py /home/upa/Descargas/prueba_doc.doc
echo '#############################################################################################################'
echo '############################################## Olevba #######################################################' 
echo '#############################################################################################################'
python /usr/local/lib/python2.7/dist-packages/oletools/olevba.py /home/upa/Descargas/prueba_docx.docx
echo '#############################################################################################################'
echo '############################################## EXIF #########################################################' 
echo '#############################################################################################################'
/opt/exiftool/./exiftool /home/upa/Descargas/putty.exe
echo '#############################################################################################################'
echo '############################################## Xortool ######################################################' 
echo '#############################################################################################################'
xortool /home/upa/Descargas/putty.exe
