import qr from 'qr-image';
import fs from 'fs';
 
var qr_svg = qr.image('https://youtu.be/XW_EAy0tr2Q', { type: 'png' });
qr_svg.pipe(fs.createWriteStream('youtube.png'));
 
var svg_string = qr.imageSync('I love QR!', { type: 'png' });
