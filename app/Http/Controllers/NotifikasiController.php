<?php

namespace App\Http\Controllers;

use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function __construct(private NotifikasiService $notifikasi)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $semua = $this->notifikasi->untuk($user);

        $tampil = $request->query('tampil') === 'belum' ? 'belum' : 'semua';
        $kategori = in_array($request->query('kategori'), ['tindakan', 'info'], true)
            ? $request->query('kategori')
            : null;
        $jenisInput = (string) $request->query('jenis');
        $jenis = array_key_exists($jenisInput, NotifikasiService::JENIS)
            ? $jenisInput
            : null;

        $daftar = $semua
            ->when($tampil === 'belum', fn ($c) => $c->where('dibaca', false))
            ->when($kategori, fn ($c) => $c->where('kategori', $kategori))
            ->when($jenis, fn ($c) => $c->where('jenis', $jenis))
            ->values();

        return view('notifikasi.index', [
            'daftar' => $daftar,
            'tampil' => $tampil,
            'kategori' => $kategori,
            'jenis' => $jenis,
            'total' => $semua->count(),
            'belumDibaca' => $semua->where('dibaca', false)->count(),
            'perTindakan' => $semua->where('kategori', 'tindakan')->where('dibaca', false)->count(),
            'perJenis' => $semua->countBy('jenis'),
            'jenisAktif' => $user->preferensiNotifikasi(),
        ]);
    }

    /** Tandai satu notifikasi dibaca lalu buka halaman tujuannya. */
    public function buka(Request $request)
    {
        $user = $request->user();
        $item = $this->notifikasi
            ->untuk($user)
            ->firstWhere('kunci', (string) $request->query('k'));

        if (! $item) {
            return redirect()
                ->route('notifikasi.index')
                ->with('info', 'Notifikasi itu sudah tidak berlaku.');
        }

        $this->notifikasi->tandaiDibaca($user, [$item['kunci']]);

        // URL berasal dari daftar yang dibuat server, bukan dari input.
        return redirect($item['url']);
    }

    public function baca(Request $request)
    {
        $validated = $request->validate(['kunci' => ['required', 'string', 'max:191']]);

        $this->notifikasi->tandaiDibaca($request->user(), [$validated['kunci']]);

        return back();
    }

    public function bacaSemua(Request $request)
    {
        $user = $request->user();

        $kunci = $this->notifikasi
            ->untuk($user)
            ->where('dibaca', false)
            ->pluck('kunci')
            ->all();

        $this->notifikasi->tandaiDibaca($user, $kunci);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
