<!-- add_product_modal.blade.php -->

<div class="modal fade bd-example-modal-lg" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Your form for adding a new product goes here -->
                @include('modal.product_modal.add_product_form', ['categories' => $categories, 'units' => $units,'page' => $page])
            </div>
        </div>
    </div>
</div>
