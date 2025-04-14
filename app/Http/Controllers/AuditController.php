<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Tracking;
use Illuminate\Http\Request;
use Faker\Factory as Faker;

class AuditController extends Controller
{
    public function index() {
        $audits = Audit::with(['tracking', 'product'])->orderBy('id','desc')->simplePaginate(10);

        return view('tracking.index', [
            'audits' => $audits
        ]);
    }
    public function create() {
        return view('tracking.create');
    }
    public function show(Audit $audit) {
        return view('tracking.show', ['audit' => $audit]);
    }
    public function store(Request $request) {
        $faker = Faker::create();
        $data = trim($request->input('audit_data'));
        $lines = explode("\n", $data);

        if (count($lines) < 5) {
            return redirect()->back()->with('error', 'Invalid input. Please enter all required fields.');
        }

        $tracking = '';
        $serial = '';
        $basket = '';
        $productControl = '';
        $title = '';

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^\d{12,}$/', $line)) {
                $tracking = $line;
            } elseif (strlen($line) == 16 && preg_match('/^[A-Z0-9]+$/', $line)) {
                $serial = $line;
            } elseif (preg_match('/^BKT[a-zA-Z0-9]+$/', $line)) {
                $basket = $line;
            } elseif (preg_match('/^PCN[a-zA-Z0-9]+$/', $line)) {
                $productControl = $line;
            } elseif (strcasecmp($line, 'TITLE') == 0) {
                $title = $line;
            } else {
                $title = $line;
            }
        }

        if ($tracking) {
            $last12Digits = substr($tracking, -12);
            $trackingRecord = Tracking::where('tracking_no', 'like', '%' . $last12Digits)->first();

            if ($trackingRecord) {
                $audit = Audit::where('tracking_id', $trackingRecord->id)->first();

                if ($audit) {
                    if ($audit->product_id) {
                        $product = Product::find($audit->product_id);

                        if ($product) {
                            $oldQty = $product->quantity;
                            $newQty = $faker->numberBetween(1, 100);

                            if (!$product->name) {
                                $product->name = $faker->word;
                            }

                            if ($oldQty != $newQty) {
                                $product->quantity = $newQty;
                                $product->save();

                                StockMovement::create([
                                    'product_id' => $product->id,
                                    'type' => ($newQty > $oldQty) ? 'in' : 'out',
                                    'quantity' => abs($newQty - $oldQty),
                                    'reference_note' => 'Stock Updated',
                                ]);
                            } else {
                                $product->save();
                            }
                        }
                    } else {
                        $product = Product::create([
                            'name' => $faker->word,
                            'sku' => $faker->word,
                            'category_id' => Category::inRandomOrder()->first()->id,
                            'quantity' => $faker->numberBetween(1, 100),
                            'unit' => $faker->word,
                            'reorder_level' => $faker->numberBetween(1, 10),
                        ]);

                        StockMovement::create([
                            'product_id' => $product->id,
                            'type' => 'in',
                            'quantity' => $product->quantity,
                            'reference_note' => 'Initial Stock setup',
                        ]);

                        $audit->product_id = $product->id;
                        $audit->save();
                    }

                    $audit->status = 'Updated';
                    $audit->serial_no = $serial;
                    $audit->basket_no = $basket;
                    $audit->product_control_no = $productControl;
                    $audit->title = $title;
                    $audit->save();

                    return redirect('/tracking')->with('success', 'Audit updated successfully!');
                } else {
                    $product = Product::create([
                        'name' => $faker->word,
                        'sku' => $faker->word,
                        'category_id' => Category::inRandomOrder()->first()->id,
                        'quantity' => $faker->numberBetween(1, 100),
                        'unit' => $faker->word,
                        'reorder_level' => $faker->numberBetween(1, 10),
                    ]);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $product->quantity,
                        'reference_note' => 'Initial Stock setup',
                    ]);

                    $audit = new Audit();
                    $audit->title = $title;
                    $audit->product_control_no = $productControl;
                    $audit->basket_no = $basket;
                    $audit->serial_no = $serial;
                    $audit->status = 'Created';
                    $audit->tracking_id = $trackingRecord->id;
                    $audit->product_id = $product->id;
                    $audit->save();

                    return redirect('/tracking')->with('success', 'Audit created successfully with new Product and existing tracking number!');
                }
            } else {
                $randomDigits = '';
                for ($i = 0; $i < 34 - strlen($tracking); $i++) {
                    $randomDigits .= rand(0, 9);
                }

                $newTrackingNo = $randomDigits . $tracking;

                $trackingRecord = new Tracking();
                $trackingRecord->tracking_no = $newTrackingNo;
                $trackingRecord->save();

                $product = Product::create([
                    'name' => $faker->word,
                    'sku' => $faker->word,
                    'category_id' => Category::inRandomOrder()->first()->id,
                    'quantity' => $faker->numberBetween(1, 100),
                    'unit' => $faker->word,
                    'reorder_level' => $faker->numberBetween(1, 10),
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $product->quantity,
                    'reference_note' => 'Initial Stock setup',
                ]);

                $audit = new Audit();
                $audit->title = $title;
                $audit->product_control_no = $productControl;
                $audit->basket_no = $basket;
                $audit->serial_no = $serial;
                $audit->status = 'Created';
                $audit->tracking_id = $trackingRecord->id;
                $audit->product_id = $product->id;
                $audit->save();

                return redirect('/tracking')->with('success', 'Audit created successfully with new tracking number, Product!');
            }
        }

        return redirect()->back()->with('error', 'Tracking number is missing.');
    }
    public function destroy($id) {
        $audit = Audit::findOrFail($id);
        $audit->delete();

        return redirect()->route('tracking.index')->with('success', 'Audit deleted successfully.');
    }
}
