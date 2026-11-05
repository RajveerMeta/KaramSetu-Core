
<?php
require_once __DIR__ . '/../../DataBase/admin/ngos/view.php';

ob_start();
?>


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <a href="<?= baseUrl('admin/ngos/index.php') ?>"
           class="inline-flex items-center text-sm font-medium text-forest hover:text-gold transition-colors mb-4">
            &larr; Back to NGOs
        </a>

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-serif font-bold text-forest mb-2">
                    NGO Details
                </h1>
                <p class="text-charcoal-light">
                    Review the organization's information and submitted documents.
                </p>
            </div>

            <span class="inline-flex w-fit items-center px-3 py-1 rounded-full text-sm font-medium border <?= $statusClass ?>">
                <?= escapeNgoValue($statusLabel) ?>
            </span>
        </div>
    </div>

    <?php if ($alertMessage !== ''): ?>
        <div class="mb-6 p-4 rounded-lg border <?= $alertType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' ?>">
            <p class="text-sm font-medium">
                <?= escapeNgoValue($alertMessage) ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- Organization Information -->
        <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                <h2 class="text-lg font-serif font-bold text-forest">
                    Organization Information
                </h2>
            </div>

            <div class="p-6">
                <dl class="space-y-4">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">NGO Name</dt>
                        <dd class="text-sm font-medium text-charcoal sm:col-span-2">
                            <?= escapeNgoValue($ngo['name']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Registration Number</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= escapeNgoValue($ngo['registration_number']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Organization Type</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= escapeNgoValue($ngoType) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Established</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= escapeNgoValue(date('d M Y', strtotime($ngo['established_date']))) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Location</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= escapeNgoValue($ngo['city']) ?>,
                            <?= escapeNgoValue($ngo['state']) ?>
                            - <?= escapeNgoValue($ngo['pincode']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Official Email</dt>
                        <dd class="text-sm sm:col-span-2 break-words">
                            <a href="mailto:<?= escapeNgoValue($ngo['official_email']) ?>"
                               class="text-forest hover:text-gold">
                                <?= escapeNgoValue($ngo['official_email']) ?>
                            </a>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Phone</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= escapeNgoValue($ngo['phone']) ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Website</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2 break-words">
                            <?php if (!empty($ngo['website'])): ?>
                                <a href="<?= escapeNgoValue($ngo['website']) ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-forest hover:text-gold">
                                    <?= escapeNgoValue($ngo['website']) ?>
                                </a>
                            <?php else: ?>
                                Not provided
                            <?php endif; ?>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                        <dt class="text-sm font-medium text-charcoal-light">Address</dt>
                        <dd class="text-sm text-charcoal sm:col-span-2">
                            <?= nl2br(escapeNgoValue($ngo['address'])) ?>
                        </dd>
                    </div>

                </dl>
            </div>
        </div>

        <div class="space-y-6">

            <!-- Primary Contact -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h2 class="text-lg font-serif font-bold text-forest">
                        Primary Contact
                    </h2>
                </div>

                <div class="p-6">
                    <dl class="space-y-4">

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="text-sm font-medium text-charcoal-light">Name</dt>
                            <dd class="text-sm font-medium text-charcoal sm:col-span-2">
                                <?= escapeNgoValue($ngo['contact_person']) ?>
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="text-sm font-medium text-charcoal-light">Position</dt>
                            <dd class="text-sm text-charcoal sm:col-span-2">
                                <?= escapeNgoValue($ngo['contact_designation']) ?>
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="text-sm font-medium text-charcoal-light">Account Name</dt>
                            <dd class="text-sm text-charcoal sm:col-span-2">
                                <?= escapeNgoValue($ngo['account_name'] ?? 'Not linked') ?>
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                            <dt class="text-sm font-medium text-charcoal-light">Account Email</dt>
                            <dd class="text-sm text-charcoal sm:col-span-2 break-words">
                                <?= escapeNgoValue($ngo['account_email'] ?? 'Not linked') ?>
                            </dd>
                        </div>

                    </dl>
                </div>
            </div>

            <!-- About Organization -->
            <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
                    <h2 class="text-lg font-serif font-bold text-forest">
                        About the Organization
                    </h2>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-forest mb-1">Primary Cause</h3>
                        <p class="text-sm text-charcoal">
                            <?= escapeNgoValue($ngo['primary_cause']) ?>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-forest mb-1">Description</h3>
                        <p class="text-sm text-charcoal leading-relaxed">
                            <?= nl2br(escapeNgoValue($ngo['description'])) ?>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-forest mb-1">Mission</h3>
                        <p class="text-sm text-charcoal leading-relaxed">
                            <?= nl2br(escapeNgoValue($ngo['mission'])) ?>
                        </p>
                    </div>

                    <?php if (!empty($ngo['impact_summary'])): ?>
                        <div>
                            <h3 class="text-sm font-semibold text-forest mb-1">Impact Summary</h3>
                            <p class="text-sm text-charcoal leading-relaxed">
                                <?= nl2br(escapeNgoValue($ngo['impact_summary'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Submitted Documents -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-charcoal/10 bg-ivory/30">
            <h2 class="text-lg font-serif font-bold text-forest">
                Submitted Documents
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-ivory/10 text-charcoal-light text-sm border-b border-charcoal/10">
                        <th class="px-6 py-3 font-medium">Document</th>
                        <th class="px-6 py-3 font-medium">Document Number</th>
                        <th class="px-6 py-3 font-medium">Submitted On</th>
                        <th class="px-6 py-3 font-medium text-right">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-charcoal/5">

                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-charcoal-light">
                                No documents have been submitted.
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php foreach ($documents as $document): ?>
                            <?php
                            $documentTypes = [
                                'registration_certificate' => 'Registration Certificate',
                                'pan_card' => 'PAN Card',
                                'address_proof' => 'Address Proof',
                                'supporting_document' => 'Supporting Document',
                                'other' => 'Other'
                            ];

                            $documentName = $documentTypes[$document['document_type']]
                                ?? 'Other';
                            ?>

                            <tr class="hover:bg-ivory/30">
                                <td class="px-6 py-4 text-sm font-medium text-charcoal">
                                    <?= escapeNgoValue($documentName) ?>
                                    <p class="text-xs text-charcoal-light mt-1">
                                        <?= escapeNgoValue($document['file_name']) ?>
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-sm text-charcoal">
                                    <?= escapeNgoValue($document['document_number'] ?: 'Not provided') ?>
                                </td>

                                <td class="px-6 py-4 text-sm text-charcoal">
                                    <?= escapeNgoValue(date('d M Y', strtotime($document['created_at']))) ?>
                                </td>

                                <td class="px-6 py-4 text-sm text-right">
                                    <?php
                                    $documentPath = ltrim($document['file_path'], '/');
                                    ?>

                                    <a href="<?= baseUrl($documentPath) ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="text-forest font-medium hover:text-gold">
                                        View Document
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Verification Status and Actions -->
    <div class="bg-white rounded-xl border border-charcoal/10 shadow-sm p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div class="max-w-2xl">
                <h2 class="text-lg font-serif font-bold text-forest mb-2">
                    Verification Status
                </h2>

                <p class="text-sm text-charcoal leading-relaxed">
                    Current status:
                    <strong><?= escapeNgoValue($statusLabel) ?></strong>
                </p>

                <?php if (!empty($ngo['rejection_reason'])): ?>
                    <p class="mt-3 text-sm text-red-700">
                        <strong>Rejection Reason:</strong>
                        <?= nl2br(escapeNgoValue($ngo['rejection_reason'])) ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ($status === 'pending'): ?>
                <div class="flex flex-col sm:flex-row gap-3">

                    <button type="button"
                            id="btnRejectInit"
                            class="px-6 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 font-medium rounded-md text-sm">
                        Reject NGO
                    </button>

                    <button type="button"
                            id="btnApproveInit"
                            class="px-6 py-2.5 bg-forest text-ivory hover:bg-forest-dark font-medium rounded-md text-sm">
                        Approve NGO
                    </button>

                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Approve Modal -->
<div id="approveModal"
     class="fixed inset-0 z-[100] hidden overflow-y-auto"
     role="dialog"
     aria-modal="true"
     aria-labelledby="approveTitle">

    <div class="fixed inset-0 bg-charcoal/60" id="approveOverlay"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white rounded-xl shadow-xl border border-charcoal/10 p-6">

            <h2 id="approveTitle" class="text-xl font-serif font-bold text-forest mb-3">
                Approve <?= escapeNgoValue($ngo['name']) ?>?
            </h2>

            <p class="text-sm text-charcoal-light mb-6">
                This will approve the NGO and activate its linked account.
            </p>

            <form method="POST">
                <input type="hidden" name="action" value="approve">

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button"
                            id="btnCancelApprove"
                            class="px-5 py-2 border border-charcoal/20 rounded-md text-sm">
                        Cancel
                    </button>

                    <button type="submit"
                            class="px-5 py-2 bg-forest text-white rounded-md text-sm hover:bg-forest-dark">
                        Confirm Approval
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal"
     class="fixed inset-0 z-[100] hidden overflow-y-auto"
     role="dialog"
     aria-modal="true"
     aria-labelledby="rejectTitle">

    <div class="fixed inset-0 bg-charcoal/60" id="rejectOverlay"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white rounded-xl shadow-xl border border-charcoal/10 p-6">

            <h2 id="rejectTitle" class="text-xl font-serif font-bold text-red-800 mb-3">
                Reject <?= escapeNgoValue($ngo['name']) ?>?
            </h2>

            <p class="text-sm text-charcoal-light mb-5">
                Provide a reason for rejecting this application.
            </p>

            <form method="POST" id="rejectForm">
                <input type="hidden" name="action" value="reject">

                <label for="rejectionReason" class="block text-sm font-medium text-charcoal mb-2">
                    Rejection Reason <span class="text-red-600">*</span>
                </label>

                <textarea name="rejection_reason"
                          id="rejectionReason"
                          rows="4"
                          required
                          class="w-full px-3 py-2 border border-charcoal/20 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-forest"
                          placeholder="Enter the reason for rejection..."></textarea>

                <p id="rejectionError" class="hidden mt-2 text-sm text-red-600">
                    Please provide a rejection reason.
                </p>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-5">
                    <button type="button"
                            id="btnCancelReject"
                            class="px-5 py-2 border border-charcoal/20 rounded-md text-sm">
                        Cancel
                    </button>

                    <button type="submit"
                            class="px-5 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">
                        Confirm Rejection
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const approveModal = document.getElementById('approveModal');
    const rejectModal = document.getElementById('rejectModal');

    const approveButton = document.getElementById('btnApproveInit');
    const rejectButton = document.getElementById('btnRejectInit');

    function openModal(modal) {
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeModal(modal) {
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    if (approveButton) {
        approveButton.addEventListener('click', function () {
            openModal(approveModal);
        });
    }

    if (rejectButton) {
        rejectButton.addEventListener('click', function () {
            openModal(rejectModal);
        });
    }

    document.getElementById('btnCancelApprove').addEventListener('click', function () {
        closeModal(approveModal);
    });

    document.getElementById('approveOverlay').addEventListener('click', function () {
        closeModal(approveModal);
    });

    document.getElementById('btnCancelReject').addEventListener('click', function () {
        closeModal(rejectModal);
    });

    document.getElementById('rejectOverlay').addEventListener('click', function () {
        closeModal(rejectModal);
    });

    const rejectionReason = document.getElementById('rejectionReason');
    const rejectionError = document.getElementById('rejectionError');

    rejectionReason.addEventListener('input', function () {
        if (this.value.trim() !== '') {
            rejectionError.classList.add('hidden');
            this.classList.remove('border-red-500');
        }
    });

    document.getElementById('rejectForm').addEventListener('submit', function (event) {
        if (rejectionReason.value.trim() === '') {
            event.preventDefault();
            rejectionError.classList.remove('hidden');
            rejectionReason.classList.add('border-red-500');
            rejectionReason.focus();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal(approveModal);
            closeModal(rejectModal);
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layouts/admin.php';
?>
