@extends('admin.admin')
@section('title','Transaksi Batal')
@section('content')

  <div id="app3" class="container-fluid">
    <div v-if="sukses">
      <h6 class="font font-weight-bold text-center text-danger text-uppercase">Batal</h6>
      <table class="table table-hover table-condensed">
        <thead>
          <tr class="font-weight-bold">
            <td>Username</td>
            <td>Kode Pemesanan</td>
            <td>Total</td>
            <td>Dipesan Pada</td>
            <td>Keterangan</td>
            <td></td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tik in pems">
            <td>@{{tik.username}}</td>
            <td>@{{tik.kode_pemesanan }}</td>
            <td>@{{tik.total}}</td>
            <td>@{{tik.created_at}}</td>
            <td>@{{tik.ket_batal}}</td>
            <td>
              <button class="btn btn-success btn-block" type="submit" v-on:click="lihat(tik.kode_pemesanan)" >
                Detail
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="Lihat" class="card">
      <div class="form-group card-body" v-for="i in confirms">
        <img :src="'/storage/images/'+i.nama_foto" alt="" width="100%">
      </div>
      <div class="card-body">
        <h6 class="font font-weight-bold text-danger">Total Transfer</h6>
        <div class="form-control" v-for="i in totals">
          @{{ i.total }}
        </div>
        <div class="card-body">
          <button class="btn btn-danger" v-on:click="daftar">Back</button>
        </div>
      </div>
    </div>
  </div>

@endsection
     
@push('js')
  <script type="text/javascript">
    var xhttp = new XMLHttpRequest();
    var token = "<?= session('token') ?>";


    var app = new Vue({
        el: '#app3',
        data: {
          sukses:true,
          Lihat:false,
          status:0,
          pems:[],
          confirms:[],
          totals:[],
        },

        beforeMount : function(){
          this.get_pemesanan();
        },
        methods : {
          look : function(){
            this.sukses=false;
            this.Lihat = true;
          },
          daftar : function(){
            this.sukses = true;
            this.Lihat=false;
          },
          get_pemesanan : function(){
            var url = "{{route('dibatalkan')}}";
            var data_token = '?token='+token;
            var data = '';

            xhttp.onreadystatechange = function(){
              if (this.readyState == 4) {
                console.log(this.status,this.responseText)

                if (this.status == 401) {
                  alert('Unauthorized User')
                
                }
                if (this.status == 200) {
                  app.pems = JSON.parse(this.responseText);
                }
              }
            }
            xhttp.open("GET", url+data_token+data, true);
            xhttp.setRequestHeader("X-CSRF-TOKEN","{{ csrf_token() }}");
            xhttp.send();
          },
          lihat : function(kode_pemesanan){
            var url = "{{route('lihat')}}";
            var data_token = '?token='+token;
            var data = '&kode_pemesanan='+kode_pemesanan;

            xhttp.onreadystatechange = function(){
              if (this.readyState == 4) {
                console.log(this.status,this.responseText)

                if (this.status == 401) {
                  alert('Unauthorized User')
                
                }
                if (this.status == 200) {
                  app.confirms = JSON.parse(this.responseText);
                  app.look();
                  app.getTotal(kode_pemesanan);
                }
              }
            }
            xhttp.open("GET", url+data_token+data, true);
            xhttp.setRequestHeader("X-CSRF-TOKEN","{{ csrf_token() }}");
            xhttp.send();
          },
          getTotal : function(kode_pemesanan){
            var url = "{{route('total_konfirmasi_admin')}}";
            var data_token = '?token='+token;
            var data = '&kode_pemesanan='+kode_pemesanan;

            xhttp.onreadystatechange = function(){
              if (this.readyState == 4) {
                console.log(this.status,this.responseText)

                if (this.status == 401) {
                  alert('Unauthorized User')
                
                }
                if (this.status == 200) {
                  app.totals = JSON.parse(this.responseText);
                }
              }
            }
            xhttp.open("GET", url+data_token+data, true);
            xhttp.setRequestHeader("X-CSRF-TOKEN","{{ csrf_token() }}");
            xhttp.send();
          },

        },
    });

  </script>
@endpush