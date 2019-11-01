<head>
    
</head>
<br>
<div class="container">
    <div class="row">
        <div class="col-md-9 col-sm-12 col-xs-9">
            <div class="row">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <a href="{!BUTTONS2!}" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Назад</a>
                </div>
                <!-- <div class="col-md-3 col-sm-3 col-xs-3">
                    <div class="panel panel-default">
                      <div class="panel-body">
                        Всего телефонов: {!PHONE_COUNT!}
                      </div>
                    </div>
                </div> -->
                
                <div class="col-md-10 col-sm-10 col-xs-10">
                    <form action="{!BUTTONS3!}" method="POST">
                        <div class="input-group ">
                          <span class="input-group-addon" id="sizing-addon2">Показать</span>
                          <input type="number" name="pagin" class="form-control" max="1000" min="5" placeholder="{!PAGIN_COUNT!} (5-1000)" aria-describedby="sizing-addon3" onchange="submit();">
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Phone (Всего: {!PHONE_COUNT!})</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {!LINE!}
                </tbody>
                
            </table>

            <!-- <nav aria-label="Page navigation">
              <ul class="pagination">
                <li>
                  <a href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li>
                  <a href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              </ul>
            </nav> -->
            <nav aria-label="...">
              <ul class="pagination">
                {!PREW!}
                {!PAGIN!}
                {!NEXT!}
              </ul>
            </nav>
        </div>
    </div>
</div>
