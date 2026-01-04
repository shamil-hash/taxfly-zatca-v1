<!DOCTYPE html>
<html>
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="description" content="Plexpay Recharge">
      <title>Plexpay Balance</title>
      @include('layouts/usersidebar')
      <style>
      table {
        border-collapse: collapse;
        width: 100%;
      }
      th, td {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
      }
      tr:nth-child(even){background-color: #f2f2f2}
      th {
        background-color: #20639B;
        color: white;
      }
      .table>thead>tr>th {
          vertical-align: bottom;
          border-bottom: 1px solid #010101;
      }
      .thumbnail {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.5);
        transition: 0.3s;
        min-width: 40%;
        border-radius: 5px;
      }

      .thumbnail-description {
        min-height: 40px;
      }

      .thumbnail:hover {
        cursor: pointer;
        box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 1);
      }
      div.nav-dat {
        font-size: 20px;
        color: #20639B;
      }

    </style>
    </head>
    <body>
      <!-- Page Content Holder -->
      <div id="content">
        <nav class="navbar navbar-default">
          <div class="container-fluid">
            <div class="navbar-header">
              <button type="button" id="sidebarCollapse" class="btn navbar-btn">
                  <i class="glyphicon glyphicon-chevron-left"></i>
                  <span></span>
              </button>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                  <li><a href="/userlogout">Logout</a></li>
              </ul>
            </div>
          </div>
        </nav>
        
        <div class="nav-dat" align="right">
          Balance {{$wallet_amount}} &nbsp; Due {{$due_amount}}
        </div>
        <br>

        <div class="dropdown" align="right">
          <button class="btn btn-default dropdown-toggle" style="padding:10px 15px;background-color:#20639B;color:white;" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            Profile
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
            <li><a href="/plexpayfunds">Funds</a></li>
            <li><a href="/plexpaytransactions">Transactions</a></li>
            <li><a href="/plexpaycollection">Collection</a></li> 
            <li><a href="/plexpayduesummary">Due Summary</a></li>
            <li><a href="/plexpaysummary">Profit Summary</a></li>
            <li role="presentation"><a href="/plexpayreports">Reports</a></li>
            <li><a href="/plexpayregister">Plexpay Register</a></li>
            <li><a href="/plexpaypasswordchange">Change Password</a></li>
          </ul>
        </div>
        <br>
        <ul class="nav nav-tabs nav-justified"> <li role="presentation" class="active"><a href="/plexpaybalance">LOCAL</a></li> <li role="presentation"><a href="/plexpayinternational">INTERNATIONAL</a></li></ul>
        <br><br>
        <div>
          <br>

          @foreach($categories as $categories)

            <div >
              <h2>{{$categories['category']}}</h2>
              <div class="row"> 

                @foreach($local_providers as $provider_info)

                  <div align="left">
                    @if($categories['id']==$provider_info['category_id'])

                      @if($categories['category']=='Prepaid')
                        <div class="col-md-1"></div>
                        <div class="col-md-5"> 
                          <div class="row">
                            <div class="col-sm-4 ">
                              <style>
                                .square
                                {
                                  /* width: 200px; */
                                  height:180px;
                                }
                              </style>
                              <div class="thumbnail">
                                <div class="caption text-center" onclick="location.href='/localprovider/<?php echo $provider_info['ProviderCode']?>'">
                                  <h4 id="thumbnail-label"><a href="" target="_blank">{{$provider_info['subcategory']}}</a></h4>
                                </div>
                                <div align="center" onclick="location.href='/localprovider/<?php echo $provider_info['ProviderCode']?>'">
                                  <img src="{{$provider_info['sub_cat_logo']}}" style="width:90px;height:70px;" alt="Prepaid Providers" />
                                </div>
                                <div class="caption card-footer text-center" onclick="location.href='/localprovider/<?php echo $provider_info['ProviderCode']?>'">
                                  <ul class="list-inline">
                                    <li></li>
                                  </ul>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        @elseif($categories['category']=='Postpaid')
                          <div class="col-md-1"></div>
                          <div class="col-md-5">
                            <div class="row">
                              <div class="col-sm-4">
                                <div class="thumbnail">
                                  <div class="caption text-center" onclick="location.href='/localcustom/<?php echo $provider_info['ProviderCode']?>'">
                                    <h4 id="thumbnail-label"><a href="" target="_blank">{{$provider_info['subcategory']}}</a></h4>
                                  </div>
                                  <div align="center" onclick="location.href='/localcustom/<?php echo $provider_info['ProviderCode']?>'">
                                    <img src="{{$provider_info['sub_cat_logo']}}" style="width:90px;height:70px;" alt="Postpaid Providers" />
                                  </div>
                                  <div class="caption card-footer text-center" onclick="location.href='/localcustom/<?php echo $provider_info['ProviderCode']?>'">
                                    <ul class="list-inline">
                                      <li></li>
                                    </ul>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
              
                        @elseif($categories['category']=='Voucher')
                          <div class="col-md-1"></div>
                          <div class="col-md-5">
                            <div class="row">
                              <div class="col-sm-4">
                                <div class="thumbnail">
                                  <input type="hidden" name="providerCode" value="{{$provider_info['subcategory']}}">
                                  <div class="caption text-center" onclick="location.href='/localvoucherrecharge/<?php echo $provider_info['subcategory']?>'">
                                    <h4 id="thumbnail-label"><a href="" target="_blank">{{$provider_info['subcategory']}}</a></h4>
                                  </div>
                                  <div align="center" onclick="location.href='/localvoucherrecharge/<?php echo $provider_info['subcategory']?>'">
                                    <img src="{{$provider_info['sub_cat_logo']}}" style="width:90px;height:70px;" alt="Voucher Providers" />
                                  </div>
                                  <div class="caption card-footer text-center" onclick="location.href='/localvoucherrecharge/<?php echo $provider_info['subcategory']?>'">
                                    <ul class="list-inline">
                                      <li></li>
                                    </ul>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
              
                        @elseif($categories['category']=='Gift Card')
                          <div class="col-md-1"></div>
                          <div class="col-md-5">
                            <div class="row">
                              <div class="col-sm-4">
                                <div class="thumbnail">
                                  <div class="caption text-center" onclick="location.href='/giftcardrecharge1/<?php echo $provider_info['ProviderCode']?>'">
                                    <h4 id="thumbnail-label"><a href="" target="_blank">{{$provider_info['subcategory']}}</a></h4>
                                  </div>
                                  <div align="center" onclick="location.href='/giftcardrecharge1/<?php echo $provider_info['ProviderCode']?>'">
                                    <img src="{{$provider_info['sub_cat_logo']}}" style="width:90px;height:70px;" alt="Giftcard Providers" />
                                  </div>
                                  <div class="caption card-footer text-center" onclick="location.href='/giftcardrecharge1/<?php echo $provider_info['ProviderCode']?>'">
                                    <ul class="list-inline">
                                      <li></li>
                                    </ul>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                        @elseif($categories['category']=='DTH')
                          <div class="col-md-1"></div>
                          <div class="col-md-5">
                            <div class="row">
                              <div class="col-sm-4">
                                <div class="thumbnail">
                                  <div class="caption text-center" onclick="location.href='internationalprovider/<?php echo $provider_info['ProviderCode']?>'">
                                    <h4 id="thumbnail-label"><a href="" target="_blank">{{$provider_info['subcategory']}}</a></h4>
                                  </div>
                                  <div align="center" onclick="location.href='internationalprovider/<?php echo $provider_info['ProviderCode']?>'">
                                    <img src="{{$provider_info['sub_cat_logo']}}" style="width:90px;height:70px;" alt="DTH Providers" />
                                  </div>
                                  <div class="caption card-footer text-center" onclick="location.href='internationalprovider/<?php echo $provider_info['ProviderCode']?>'">
                                    <ul class="list-inline">
                                      <li></li>
                                    </ul>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                      @elseif($categories['category']=='Electricity')
                        <div class="col-md-1"></div>
                        <div class="col-md-5">
                          <div class="row">
                            <div class="col-sm-4">
                              <div class="thumbnail">
                                <div class="caption text-center" onclick="location.href='/plexpayrechargeinternational/2/<?php echo $provider_info['CountryIso']?>/<?php echo $provider_info['ProviderCode']?>'">
                                  <h4 id="thumbnail-label"><a href="" target="_blank">{{$provider_info['subcategory']}}</a></h4>
                                </div>
                                <div align="center" onclick="location.href='/plexpayrechargeinternational/2/<?php echo $provider_info['CountryIso']?>/<?php echo $provider_info['ProviderCode']?>'">
                                  <img src="{{$provider_info['sub_cat_logo']}}" style="width:90px;height:70px;" alt="Electricity Providers" />
                                </div>
                                <div class="caption card-footer text-center" onclick="location.href='/plexpayrechargeinternational/2/<?php echo $provider_info['CountryIso']?>/<?php echo $provider_info['ProviderCode']?>'">
                                  <ul class="list-inline">
                                    <li></li>
                                  </ul>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      <!--  -->
                      @else
                        <div class="col-md-1"></div>
                        <div class="col-md-5">
                          <div class="row">
                            <div class="col-sm-4">
                              <div class="thumbnail">
                                <div class="caption text-center" onclick="location.href='/giftcardrecharge1/<?php echo $provider_info['ProviderCode']?>'">
                                  <h4 id="thumbnail-label"><a href="" target="_blank">{{$provider_info['subcategory']}}</a></h4>
                                </div>
                                <div align="center" onclick="location.href='/giftcardrecharge1/<?php echo $provider_info['ProviderCode']?>'">
                                  <img src="{{$provider_info['sub_cat_logo']}}" style="width:90px;height:70px;" alt="Gaming card Providers" />
                                </div>
                                <div class="caption card-footer text-center" onclick="location.href='/giftcardrecharge1/2?>'">
                                  <ul class="list-inline">
                                    <li></li>
                                  </ul>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      @endif

                    @endif
                  </div>

                @endforeach
            
              </div>
            </div>

          @endforeach
        </div>
        <br>
    </body>
</html>
