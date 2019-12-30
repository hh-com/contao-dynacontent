
document.addEventListener("DOMContentLoaded", function(event) {

    /* set loading icon when content is loading */
    document.getElementById('loadingicon').className ="load";

    /* prevent page root from click */
    var list = document.querySelectorAll('a.dontClick');
    list.forEach(function (el, index) {
        el.onclick = function () { return false; };
    });

    /* prevent page root from click */
    var list = document.querySelectorAll('a.usernotallowed');
    list.forEach(function (el, index) {
        el.onclick = function () { return false; };
    });

    document.cookie = "newItemBe= ; expires = Thu, 01 Jan 1970 00:00:00 GMT"
    //document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';

});

/* Update iframe size after loading is completed */
document.addEventListener('readystatechange', () => {    
    if (document.readyState == 'complete') {
        var iframes = document.querySelectorAll(".dyFrame");
        var ifr;
        for( var i = 0; i < iframes.length; i++) {
            /**
            * Content element is visible or not
            * Default its open
            */
            if (localStorage.getItem(iframes[i].id) === "true" || localStorage.getItem(iframes[i].id) === null) {

                if (iframes[i].closest(".edit").classList.contains('new')) {
                    iframes[i].closest(".edit").classList.remove("closed");
                    localStorage.setItem(iframes[i].id, false)
                }
                else {
                    iframes[i].closest(".edit").classList.toggle("closed");
                }
            }
            
            /* Resize each iFrame after page is fully loaded */
            ifr = iFrameResize({ 
                log: false, 
                autoResize: true,
                onResized: function(event) {
                    if (document.getElementById(event.id).contentWindow.document.body.querySelectorAll('.tl_error').length > 0) {
                        document.getElementById(event.id).closest(".edit").classList.add("hasError");
                    } else {
                        document.getElementById(event.id).closest(".edit").classList.remove("hasError");
                    }
                }
            }, "#"+iframes[i].id )
        }
        document.getElementById('loadingicon').classList.remove("load");
    }   
});

/**
 * Save all Changes on clik on .dysaveall
 */
document.addEventListener('click', function (event) {

    if (!event.target.matches('.dysaveall')) return;

    var iframes = document.querySelectorAll(".dyFrame");

    for( var i = 0; i < iframes.length; i++) {
        iframes[i].contentWindow.document.getElementById("save").click();
    }
});

// Listen for click events
document.addEventListener('click', function (event) {

	// Make sure clicked element is our toggle
    if (!event.target.classList.contains('toggleTypeElem')) return;
    
    /* Open/Close iFrame and set localStorage */
    var elemid = event.target.getAttribute("data-togglecontrol");
    var status = document.getElementById(event.target.closest('.edit').id).classList.toggle("closed");
    localStorage.setItem(elemid, status)
    
});

/**
 * Drag and Drop the contentelements
 */
document.addEventListener("DOMContentLoaded", function(event) {

    var draggables = document.querySelectorAll('[draggable=true]');
    var dragSourceElement = null;

    for (var i = 0; i < draggables.length; i++) {
        draggables[i].dataset.dragindex = i;
        
        draggables[i].addEventListener('dragstart', dragStarted);
        draggables[i].addEventListener('dragenter', dragEnter);
        draggables[i].addEventListener('dragover', dragOver);
        draggables[i].addEventListener('dragleave', dragLeave);
        draggables[i].addEventListener('dragend', dragEnd);
        draggables[i].addEventListener('drop', drop);
    }

    function dragStarted(event) {  

        for (var i = 0; i < draggables.length; i++) {
            draggables[i].classList.add('dropable');
            }

        this.style.opacity = '0.4';
        dragSourceElement = this;
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/html', this.outerHTML );
    }

    function dragOver(event) {
        if (event.preventDefault) {
            event.preventDefault(); // Necessary. Allows us to drop.
        }
        event.dataTransfer.dropEffect = 'move';
        return false;
    }

    function dragEnter(event) {
        //console.log(event.target.getBoundingClientRect());
        //console.log(event.clientX);
        this.classList.add('over');
    }

    function dragLeave(event) {
        this.classList.remove('over');
    }

    function dragEnd(event) {
        for (var i = 0; i < draggables.length; i++) {
            draggables[i].classList.remove('over');
            draggables[i].classList.remove('dropable');
        }
        this.style.opacity = '1';
    }

    function drop(event) {
        if (event.stopPropagation) {
            event.stopPropagation(); 
        }
        if (dragSourceElement != this) {
            //dragSourceElement.outerHTML  = this.outerHTML ;
            this.outerHTML  = event.dataTransfer.getData('text/html');
            var dropElement = this.querySelector(".dragdrop");
            var dragElement = dragSourceElement.querySelector(".dragdrop");
            window.location.href = dragElement.href + "&pid="+dropElement.dataset.pid;
        }
        return false;
    }
});

