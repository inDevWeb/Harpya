<?php


class HWebcam 
{
	private $width;
	private $height;
	private $input;
	
	

            
    function __construct($width,$height){

			
         
           $this->width = $width;
           $this->height = $height;

      }

      public function setInput($input){
      	$this->input = $input;
      }

      public function show(){

			TScript::create("

			 var player = document.getElementById('player'); 
			  var snapshotCanvas = document.getElementById('snapshot');
			  var captureButton = document.getElementById('capture');
			

			  var handleSuccess = function(stream) {
			
			    player.srcObject = stream;
			  };

			  $('#capture').on('click', function() {
			    var context = snapshot.getContext('2d');
	
			    context.drawImage(player, 0, 0, snapshotCanvas.width, 
			        snapshotCanvas.height);

			        showValue(snapshotCanvas.toDataURL());
			  });

			  navigator.mediaDevices.getUserMedia({video: true})
			      .then(handleSuccess);

			   function showValue(val){

			   	$.post('hlib.php',{data:val})
			   	.then(res => $('#{$this->input->id}').val(res))
			   	.catch(err => console.log(err))
			  


			   };   
				");

  
        $video = new TElement('video');
        $video->{id} = 'player';
      	$video->{controls} ='true';
      	$video->{autoplay} ='true';
      	$video->{width} = $this->width;
      	$video->{height} = $this->height;

		$button = new TButton('gravar');

	    $button->setLabel('capture');
      	$canvas = new TElement('canvas');

      	$canvas->{width} = '320';
      	$canvas->{height} = '240';

      	
      	$button->{id} = 'capture';
      	$canvas->{id} = 'snapshot';
      


        THBox::pack($video,$button,$canvas)->show();
         


      }                      
                                  
}
                              
