(function($){
/*----------------------------------------------------------------------------------
Class: FloatObject
-------------------------------------------------------------------------------------*/
	function FloatObject(jqObj, params)
	{
		this.jqObj = jqObj;
		
		switch(params.speed)
		{
			case 'fast': this.steps = 5; break;
			case 'normal': this.steps = 10; break;
			case 'slow': this.steps = 20; break;
			default: this.steps = 10;
		};
		
		var offset = this.jqObj.offset();
		this.currentX = offset.left;
		this.currentY = offset.top;
		this.width = this.jqObj.width();
		this.height = this.jqObj.height();
		this.alwaysVisible = params.alwaysVisible;
		this.alwaysTop = params.alwaysTop;
		
		
		this.origX = typeof(params.x) == "string" ?  this.currentX : params.x;
		this.origY = typeof(params.y) == "string" ?  this.currentY : params.y;
				
		
		//now we make sure the object is in absolute positions.
		this.jqObj.css({'position':'absolute' , 'top':this.currentY ,'left':this.currentX});
	}
	
	FloatObject.prototype.updateLocation = function()
	{
		this.updatedX = $(window).scrollLeft() + this.origX;
		
		if( this.alwaysTop == false ){
			this.updatedY = $(window).scrollTop()+ this.origY;
			
			if( this.alwaysVisible ){
				if( this.origX + this.width > this.windowWidth() ) 
					this.updatedX = this.windowWidth() - this.width + $(window).scrollLeft();
				if( this.origY + this.height > this.windowHeight() ) 
				{
					this.updatedY = $(window).scrollTop() + this.windowHeight() - this.height;
					if( this.updatedY < this.origY ) this.updatedY = this.origY;
				}				
			}
		}
		else
		{

			if( $(window).scrollTop() > this.origY )
			{
				this.updatedY = $(window).scrollTop() + 5;
			}
			else
			{
				this.updatedY = this.origY + 5;
			}
		}
		this.dx = Math.abs(this.updatedX - this.currentX );
		this.dy = Math.abs(this.updatedY - this.currentY );
		
		return this.dx || this.dy;
	}
	
	FloatObject.prototype.windowHeight = function()
	{
		var de = document.documentElement;
		
		return self.innerHeight ||
			(de && de.clientHeight) ||
			document.body.clientHeight;
	}
	
	FloatObject.prototype.windowWidth = function()
	{
		var de = document.documentElement;
		
		return self.innerWidth ||
			(de && de.clientWidth) ||
			document.body.clientWidth;
	}
	
	
	FloatObject.prototype.move = function()
	{
		if( this.jqObj.css("position") != "absolute" ) return;
		var cx = 0;
		var cy = 0;
		
		if( this.dx > 0 )
		{			
			if( this.dx < this.steps / 2 )
				cx = (this.dx >= 1) ? 1 : 0;
			else
				cx = Math.round(this.dx/this.steps);
			
			if( this.currentX < this.updatedX )
				this.currentX += cx;
			else
				this.currentX -= cx;
		}
		
		if( this.dy > 0 )
		{
			if( this.dy < this.steps / 2 )
				cy = (this.dy >= 1) ? 1 : 0;
			else
				cy = Math.round(this.dy/this.steps);
			
			if( this.currentY < this.updatedY )
				this.currentY += cy;
			else
				this.currentY -= cy;
		}
		
		this.jqObj.css({'left':this.currentX, 'top': this.currentY });			
	}

	
	
/*----------------------------------------------------------------------------------
Object: floatMgr
-------------------------------------------------------------------------------------*/		
	$.floatMgr = {
		
		FOArray: new Array() ,
		
		timer: null ,
		
		initializeFO: function(jqObj,params) 
		{
			var settings =  $.extend({
				x: 0 ,
				y: 0 ,
				speed: 'normal'	,
				alwaysVisible: false ,
				alwaysTop: false},params||{});
			var newFO = new FloatObject(jqObj,settings);
			
			$.floatMgr.FOArray.push(newFO);
			
			if( !$.floatMgr.timer ) $.floatMgr.adjustFO();
			
			//now making sure we are registered to all required window events
			if( !$.floatMgr.registeredEvents ) 
			{
					$(window).bind("resize", $.floatMgr.onChange);
					$(window).bind("scroll", $.floatMgr.onChange);
					$.floatMgr.registeredEvents = true;
			}		
		} , 
		
		adjustFO: function() 
		{
			$.floatMgr.timer = null;
			
			var moveFO = false;
			
			for( var i = 0 ; i < $.floatMgr.FOArray.length ; i++ )
			{
				 FO = $.floatMgr.FOArray[i];
				 if( FO.updateLocation() )  moveFO = true;
			}
			
			if( moveFO )
			{
				for( var i = 0 ; i < $.floatMgr.FOArray.length ; i++ )
				{
					FO = $.floatMgr.FOArray[i];
					FO.move();
				}
				
				if( !$.floatMgr.timer ) $.floatMgr.timer = setTimeout($.floatMgr.adjustFO,50);
			}
		}	,
		
		stopFloatChk: false ,
		
		onChange: function()
		{
			if( !$.floatMgr.timer && !$.floatMgr.stopFloatChk ) $.floatMgr.adjustFO();
		} 		
	};
	
/*----------------------------------------------------------------------------------
Function: makeFloat
-------------------------------------------------------------------------------------*/		
	$.fn.makeFloat = function(params) {
		var obj = this.eq(0); //we only operate on the first selected object;
		$.floatMgr.initializeFO(obj,params); 
		if( $.floatMgr.timer == null ) $.floatMgr.adjustFO();
		return obj;
	};
	$.fn.stopFloat = function(params) {
		$.floatMgr.stopFloatChk = true;
	};
	
	$.fn.restartFloat = function(params) {
		$.floatMgr.stopFloatChk = false;
	};
})(jQuery);