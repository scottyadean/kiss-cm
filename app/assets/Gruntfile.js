module.exports = function(grunt) {

    //vendor src path
    var vendorSrc = 'vendor/';
    
    //local src paths
    var cssSrc = 'css/';
    var jsSrc  = 'js/';

    //compressed output
    var buildCss = '../../public/css/';
    var buildJs  = '../../public/js/';

    // local JS files
    var jsFiles = [
            'app.js'
    ].map(function(file) {return jsSrc + file;});
    
    // local css files
    var cssFiles = [
            'app.css',
    ].map(function(file) {return cssSrc + file;});
    
    // vendor js libs under vendor dir.
    var jsVendorLibs = [
            'jquery/jquery.js',
            //'jquery/html5-3.6-respond-1.1.0.min.js',
            //'underscore/underscore.js',
            //'socket.io-client/dist/socket.io.js',
            'bootstrap/bootstrap.js',
   
    //append js libs 
    ].map(function(lib) {
            return vendorSrc + lib;
    });
    
    var cssVendorLibs = [
            'bootstrap/bootstrap-theme.css',
            'bootstrap/bootstrap.css'
    ].map(function(lib) {
            return vendorSrc + lib;
    });
    
    
    
    
    //task config
    grunt.initConfig({
		
        pkg: grunt.file.readJSON('package.json'),
		
		clean: {        dev: [ //buildJs  + '*',
                               //buildCss + 'themes/default/app.min.css',
                               //buildCss + 'themes/default/app.css',
                               //"!"+buildCss +'views',
                               //"!"+buildJs  +"vendor",
                               //"!"+buildCss +"vendor"
                               ]
               },
                
                sass: {dist: {
                        files:{
                             'css/app.css' : 'css/app.scss',
                             'css/views/index/index.css' : 'css/views/index/index.scss'
                        }
                        }
                },
		
            concat: {

                    js : {
                        files: [
                            
                                /* uncomment to update vendor libs
                                {src: jsVendorLibs, 
                                  dest: buildJs + 'vendor/lib.min.js'},
                                 */    
                                {src: jsFiles,  
                                    dest: buildJs + 'app.js'},
                                
                                {src: jsSrc + 'views/index/index.js',  
                                    dest: buildJs + 'views/index/index.js'},
                                    
                                {src: jsSrc + 'views/auth/join.js',  
                                    dest: buildJs + 'views/auth/join.js'},    
                                    
                            ],
                    },
                    
                    css: {
                        files: [
                                /* uncomment to update vendor libs
                                {src: cssVendorLibs, 
                                    dest: buildCss + 'vendor/lib.css'},
                                */
                                {src: cssFiles,      
                                    dest: buildCss + 'themes/default/app.css'},
                                    
                                {src: cssFiles,      
                                    dest: buildCss + 'themes/default/views/index/index.css'},    
                                    
                                    
                            ], 
                    }
            },
        
                uglify: {
                
                    js : {
                        files: [
                                {src: jsVendorLibs, dest: buildJs + 'vendor/lib.min.js'},
                                {src: jsFiles,      dest: buildJs + 'app.min.js'},
                            ],
                    },
                
                },
        
                cssmin: {
                
                
                    css: {
                        files: [
                            
                            
                               /* uncomment to update vendor libs
                                {src: buildCss + 'vendor/lib.css', 
                                   dest: buildCss + 'vendor/lib.min.css'},
                                 */
                               
                                {src: buildCss + 'themes/default/app.css', 
                                    dest: buildCss + 'themes/default/app.min.css'},
                               
                                {src: buildCss + 'themes/default/views/index/index.css', 
                                    dest: buildCss + 'themes/default/views/index/index.min.css'},
                               
                                
                            ], 
                    }
		},
                
                 sprite:{
                    
                    //for config options see: https://github.com/Ensighten/grunt-spritesmith
                    
                    all: {
                      src: cssSrc+'sprites/*.png',
                      destImg: cssSrc+'sprites.png',
                      destCSS: cssSrc+'sprites.css',
                      imgPath: 'images/sprite.png',
                      algorithm: "binary-tree",
                      padding: 2,
                      cssVarMap: function (sprite) {
                        sprite.name = 'me-' + sprite.name;
                      },
                      
                      imgOpts: {
                        'format': 'png',
                        'quality': 100,
                     },
                      
                    }
                  },
                
		watch: {
			dev: {
                files: [jsSrc+'app.js',
                        jsSrc+'views/**/*',
                        cssSrc+'**/*'],
                tasks: ['prepare', 'watch' , 'sass']
			}
		}	
	});
	
    // Load plugins
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-spritesmith');
    
    //reg our tasks.
    grunt.registerTask('prepare', ['clean:dev', 'sass', 'concat', 'uglify', 'cssmin']);
    grunt.registerTask('default', ['prepare', 'watch']);
    
}
