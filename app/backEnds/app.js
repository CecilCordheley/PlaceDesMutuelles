import {createServer} from 'node:http'
const server=createServer((req,res)=>{
    res.end();
})
server.listen("8888")