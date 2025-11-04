<?php

use App\Domain\CDE\Document;
use App\Domain\Process\ExternalApproval;
use App\Domain\Process\Gate;
use App\Services\GateService;

it('prevents gate when documents missing approval', function () {
    $service = new GateService();
    $gate = new Gate('gate-1', 'project-1', 'phase-1', 'open');
    $documents = [
        new Document('doc-1', 'project-1', 'A-001', 'Situationsplan', 'C', 'A4', [], 'draft', null),
    ];

    $result = $service->validate($gate, $documents, [], []);

    expect($result['ok'])->toBeFalse()
        ->and($result['reasons'])->toContain('All documents in the phase must be approved_to_publish.');
});

it('prevents gate when external approval missing', function () {
    $service = new GateService();
    $gate = new Gate('gate-1', 'project-1', 'phase-1', 'open');
    $documents = [
        new Document('doc-1', 'project-1', 'A-001', 'Situationsplan', 'C', 'A4', [], 'approved_to_publish', 'v1'),
    ];

    $result = $service->validate($gate, $documents, [], []);

    expect($result['ok'])->toBeFalse()
        ->and($result['reasons'])->toContain('External approval is required before passing the gate.');
});

it('allows gate when requirements met', function () {
    $service = new GateService();
    $gate = new Gate('gate-1', 'project-1', 'phase-1', 'open');
    $documents = [
        new Document('doc-1', 'project-1', 'A-001', 'Situationsplan', 'C', 'A4', [], 'approved_to_publish', 'v1'),
    ];
    $approvals = [
        new ExternalApproval('ext-1', 'phase-1', ['email' => 'approver@example.com'], new DateTimeImmutable()),
    ];

    $result = $service->validate($gate, $documents, $approvals, []);

    expect($result['ok'])->toBeTrue()
        ->and($result['reasons'])->toBeEmpty();
});
