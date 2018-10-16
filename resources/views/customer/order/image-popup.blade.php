<div id="image-modal-{{$item['id']}}" class="modal image-modal">
    <div class="modal-content" style="margin: -70px auto;height:750px">
        @foreach($item->images as $key=>$image)
        <div class="mySlides">
            <img src="{{$image->path }}">
        </div>
        @endforeach
        <a class="prev" onclick="plusSlides(this,-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(this,1)">&#10095;</a>

        <div class="images-thumb" style="margin-top: 20px;clear: both; display: block; float: left; text-align: center;">
            @foreach($item->images as $key=>$image)
                <div class="column" style="height: 100px;">
                    <img class="demo cursor" src="{{$image->path }}" style="height: 100px" onclick="currentImage(this,{{$key + 1}})">
                </div>
            @endforeach
        </div>
        <span class="close cursor" data-dismiss="modal">&times;</span>
    </div>
</div>
