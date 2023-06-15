<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WebinfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('webinfo')->insert([
            'name' => 'apiDocs',
            'value' => `<p>Please note that all request from our platform are using <strong>POST&nbsp;</strong>and use JSON encoded to send in.</p>
<p>By sending request to our server. You had to include 1 parameter which is&nbsp;<strong>"token"</strong> in order to perform any route provided below</p>
<p>&nbsp;</p>
<p>Next, I'm gonna see the example of general response:</p>
<p>Success:</p>
<pre class="language-javascript"><code>{
     "status": "200",
     "success": true,
     "message": null,
     "data": {
          // Set of data here.
     }
}</code></pre>
<p>&nbsp;</p>
<p>Failed:</p>
<pre class="language-javascript"><code>{
     "status": "502",
     "success": false,
     "message": "Some error description here",
     "data": []
}</code></pre>
<hr>
<p><span style="font-size: 12pt;"><strong>POST </strong>/user/info</span></p>
<p><span style="font-size: 12pt;">- Return provided user's balance</span></p>
<div>- Successful response</div>
<div>
<div>
<div>
<pre class="language-javascript"><code>[
       {
              "balance": "50000"
       }
]</code></pre>
</div>
</div>
</div>
<hr>
<p><strong><span style="font-size: 12pt;">POST</span></strong><span style="font-size: 12pt;"> /sim/services</span></p>
<p>- Return list of services available</p>
<p>- Successful response</p>
<pre class="language-javascript"><code>[
       {
              "id": "0ce186ff61",
              "name": "Google",
              "price": "1000"
       },
       // ...
]</code></pre>
<hr>
<p><strong><span style="font-size: 12pt;">POST</span></strong><span style="font-size: 12pt;"> /sim/networks</span></p>
<p>- Return list of networks available</p>
<p>- Successful response</p>
<pre class="language-javascript"><code>[
       {
              "id": "0ce186ff61",
              "name": "Google",
              "price": "1000"
       },
       // ...
]</code></pre>
<hr>
<p><strong><span style="font-size: 12pt;">POST</span></strong><span style="font-size: 12pt;"> /sim/rent</span></p>
<p>- Rent service from our system</p>
<p>- Additional parameters</p>
<p><span style="font-size: 10pt;"><strong>&nbsp; &nbsp; &nbsp; &nbsp; service | REQUIRED</strong>: 10 character string. Can be obtain from request /sims/services.</span></p>
<p><span style="font-size: 10pt;"><strong>&nbsp; &nbsp; &nbsp; &nbsp; network | REQUIRED</strong>: Fill <strong>"all"</strong> to let the system pick <em>or</em> 10 character string. Can be obtain from request /sims/networks.</span></p>
<p><span style="font-size: 10pt;"><strong>&nbsp; &nbsp; &nbsp; &nbsp; number| NULLABLE</strong>: Rent a specicfic number, can only be rent when that number is available.</span></p>
<p>- Successful response</p>
<pre class="language-javascript"><code>{
     "phone": "9202548316",
     "country": "84",
     "balance": 46000,
     "price": "1000",
     "name": "Google",
     "requestId": "627e74d573",
     "createdTime": "2023-06-11 21:24:58"
}</code></pre>
<hr>
<p><strong><span style="font-size: 12pt;">POST</span></strong><span style="font-size: 12pt;"> /sim/rent</span></p>
<p>- Getting rent's request information using unique ID</p>
<p>- Additional parameters</p>
<p><span style="font-size: 10pt;"><strong>&nbsp; &nbsp; &nbsp; &nbsp; requestId | REQUIRED</strong>: 10 character string. Can be obtain from request /sims/rent.</span></p>
<p>- Successful response</p>
<pre class="language-javascript"><code>{
     "requestId": "6a27e9a4ba",
     "phoneNumber": "9202548316",
     "countryCode": "84",
     "serviceId": "Google",
     "serviceName": "Google",
     "status": "2",
     "createdTime": "2023-06-11 21:24:58",
     "code": null,
}</code></pre>`
        ]);
    }
}
