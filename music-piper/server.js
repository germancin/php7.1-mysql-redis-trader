require('dotenv').config()
const express = require('express')
const helmet = require('helmet')
const http = require('http')
const app = express()
const renderForestRoutes = require('./routes/renderForestRoutes')
const musicRoutes = require('./routes/musicRoutes')

const port = (process.env.PORT !== undefined)? process.env.PORT : 7778

/**
 * secures the express app
 * setting up several HTTP headers.
 */
app.use(helmet())

/**
 * It parses incoming requests
 * with JSON payloads and is based on body-parser
 */
app.use(express.json())

app.use( (req, res, next) => {
    let  allowedOrigins = [`http://localhost:${port}`, 'http://142.93.115.215']
    let  origin = req.headers.origin
    if (allowedOrigins.indexOf(origin) > -1) {
        res.setHeader('Access-Control-Allow-Origin', origin)
    }
    res.setHeader('Access-Control-Allow-Headers', "Authorization, Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Headers")
    res.setHeader('Access-Control-Allow-Methods', 'GET'); // For this API GET will be enough.
    res.setHeader('Access-Control-Allow-Credentials', false); // Authorization is not required.
    next();
});

const base_path = '/api/v1/'

app.use(base_path + 'render', renderForestRoutes)
app.use(base_path + 'music', musicRoutes)

app.listen(port, () => {
    console.log(`Server listening on ${port}`)
});