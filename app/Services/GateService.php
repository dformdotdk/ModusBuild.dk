<?php

namespace App\Services;

use App\Domain\CDE\Document;
use App\Domain\Process\ExternalApproval;
use App\Domain\Process\Gate;

class GateService
{
    /**
     * Validate whether a gate can be passed.
     *
     * @param array<int, Document> $documents
     * @param array<int, ExternalApproval> $approvals
     * @param array<int, array<string, mixed>> $openWorkflowSteps
     *
     * @return array{ok: bool, reasons: array<int, string>}
     */
    public function validate(Gate $gate, array $documents, array $approvals, array $openWorkflowSteps): array
    {
        $reasons = [];

        $missingApprovals = array_filter($documents, fn (Document $doc) => $doc->status !== 'approved_to_publish');
        if (!empty($missingApprovals)) {
            $reasons[] = 'All documents in the phase must be approved_to_publish.';
        }

        if (empty($approvals)) {
            $reasons[] = 'External approval is required before passing the gate.';
        }

        if (!empty($openWorkflowSteps)) {
            $reasons[] = 'All workflow steps must be completed before passing the gate.';
        }

        return [
            'ok' => empty($reasons),
            'reasons' => $reasons,
        ];
    }
}
