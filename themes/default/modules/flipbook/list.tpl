<!-- BEGIN: main -->
<style>
    .list-book {
        display: flex;
        flex-wrap: wrap;
    }
    
    .book-item {
        display: block;
        background-color: #fefefe;
        margin: 0 0 20px;
        padding: 10px 10px 15px;
        border-radius: 8px;
        box-shadow: 0 10px 20px 0 rgba(30, 30, 30, .07);   
    }
    

   .book-item {
        width: calc(33.333% - 13.333px);
        margin-right: 20px
    }
    
    .book-item:nth-of-type(3n) {
        margin-right: 0
    }
    
    .book-img {
        position: relative;
        overflow: hidden;
        padding-top: 56.25%;
        margin-bottom: 10px;
    }
    
    img.book-item__img {
        position: absolute;
        height: 100%;
        width: 100% ;
        left: 0;
        top: 0;
        object-fit: cover;
    }
    
    p.info_book-time {
        font-size: 1.4rem;
        color: #454545;
    }

    @media screen and (max-width:920px){
        .book-item {
            width: calc(50% - 10px)
        }
    
        .book-item:nth-of-type(3n) {
            margin-right: 20px
        }
    
        .book-item:nth-of-type(2n),
        .book-item:nth-of-type(6n){
            margin-right: 0
        }
        
    }

    @media screen and (max-width:920px){
        .book-item {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
<div class="list-book">
 <!-- BEGIN: loop -->
    <div class="book-item">
        <div class="book-img">
             <a href="{ROW.link}" title="{ROW.title}">
                <img src="{ROW.image}" alt="{ROW.title}"  class="book-item__img"/>
            </a>
        </div>
        <div class="info_book">
            <h2 class="info_book-title">
              <a href="{ROW.link}" title="{ROW.title}">{ROW.code}</a>
            </h2>
            <!-- BEGIN: publtime -->
              <p class="info_book-time">  <span class="label-name">{LANG.publtime}:</span>{ROW.publtime}</p>
            <!-- END: publtime -->
        </div>
    </div>
    <!-- END: loop -->
<!-- BEGIN: generate_page -->
<div class="generate_page">
    {GENERATE_PAGE}
</div>
<!-- END: generate_page -->
</div>

<!-- END: main -->
