<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="row">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Produk</p>
                    <h5 class="font-weight-bolder">
                      {{ $totalProducts }}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                    <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Transaksi Hari Ini</p>
                    <h5 class="font-weight-bolder">
                      {{ $todayTransactions }}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                    <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-sm-6">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Pendapatan Hari Ini</p>
                    <h5 class="font-weight-bolder">
                      Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <!-- Grafik Pendapatan Bulanan -->
        <div class="col-12 col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Pendapatan 6 Bulan Terakhir</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="revenue-chart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Terlaris -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>5 Produk Terlaris</h6>
                </div>
                <div class="card-body p-3">
                    @if(count($topProducts) > 0)
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-end">Terjual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $product)
                                        <tr>
                                            <td class="text-sm">{{ $product['name'] }}</td>
                                            <td class="text-sm text-end">{{ $product['total'] }} pcs</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-muted">Belum ada data penjualan produk.</p>
                    @endif
                </div>
            </div>
        </div>
      </div>

      <!-- Produk Stok Sedikit -->
      <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Produk Stok Sedikit (≤ 10)</h6>
                </div>
                <div class="card-body p-3">
                    @if(count($lowStockProducts) > 0)
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Stok Tersisa</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                        <tr class="{{ $product['stock'] <= 3 ? 'table-danger' : 'table-warning' }}">
                                            <td class="text-sm">{{ $product['name'] }}</td>
                                            <td class="text-sm text-center">
                                                <span class="badge bg-gradient-{{ $product['stock'] <= 3 ? 'danger' : 'warning' }}">
                                                    {{ $product['stock'] }} pcs
                                                </span>
                                            </td>
                                            <td class="text-sm text-end">Rp {{ number_format($product['price'], 0, ',', '.') }}</td>
                                            <td class="text-sm text-end">
                                                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ni ni-cart"></i> Restok
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="ni ni-check-bold"></i> Semua stok produk dalam jumlah aman.
                        </div>
                    @endif
                </div>
            </div>
        </div>
      </div>

      <!-- Pesan Selamat Datang -->
      <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Selamat Datang, {{ auth()->user()->name }}!</h5>
                    <p class="card-text">Anda login sebagai {{ auth()->user()->role }}. Gunakan menu di samping untuk mengelola toko Anda.</p>
                </div>
            </div>
        </div>
      </div>

      @push('scripts')
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
          // Grafik Pendapatan
          const revenueCtx = document.getElementById('revenue-chart').getContext('2d');
          new Chart(revenueCtx, {
              type: 'line',
              data: {
                  labels: @json($revenueMonths),
                  datasets: [{
                      label: 'Pendapatan',
                      data: @json($revenueData),
                      backgroundColor: 'rgba(45, 206, 137, 0.1)',
                      borderColor: '#2dce89',
                      borderWidth: 2,
                      tension: 0.3,
                      fill: true
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      legend: {
                          display: true,
                          position: 'top'
                      },
                      tooltip: {
                          callbacks: {
                              label: function(context) {
                                  return 'Rp ' + context.raw.toLocaleString('id-ID');
                              }
                          }
                      }
                  },
                  scales: {
                      y: {
                          beginAtZero: true,
                          ticks: {
                              callback: function(value) {
                                  return 'Rp ' + value.toLocaleString('id-ID');
                              }
                          }
                      }
                  }
              }
          });
      </script>
      @endpush

</x-app-layout>
