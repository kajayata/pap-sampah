import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../models/landfill.dart';
import '../../models/waste_bank.dart';
import '../../providers/public_provider.dart';

class WasteBanksScreen extends StatefulWidget {
  final Function(double lat, double lng)? onNavigateToMap;

  const WasteBanksScreen({super.key, this.onNavigateToMap});

  @override
  State<WasteBanksScreen> createState() => _WasteBanksScreenState();
}

class _WasteBanksScreenState extends State<WasteBanksScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final TextEditingController _searchCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final prov = Provider.of<PublicProvider>(context, listen: false);
      prov.loadWasteBanks();
      prov.loadLandfills();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    _searchCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Bank Sampah & TPA', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: AppTheme.secondaryColor,
          indicatorWeight: 3,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          labelStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
          tabs: const [
            Tab(icon: Icon(Icons.recycling_rounded, size: 20), text: 'Bank Sampah'),
            Tab(icon: Icon(Icons.delete_sweep_rounded, size: 20), text: 'TPA Sumbersari'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildWasteBankTab(),
          _buildLandfillTab(),
        ],
      ),
    );
  }

  Widget _buildWasteBankTab() {
    return Consumer<PublicProvider>(
      builder: (context, prov, child) {
        return Column(
          children: [
            // Search Input
            Container(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
              color: Colors.white,
              child: TextField(
                controller: _searchCtrl,
                decoration: InputDecoration(
                  hintText: 'Cari bank sampah atau kelurahan...',
                  prefixIcon: const Icon(Icons.search_rounded, color: AppTheme.primaryColor),
                  suffixIcon: _searchCtrl.text.isNotEmpty
                      ? IconButton(
                          icon: const Icon(Icons.clear, size: 18),
                          onPressed: () {
                            _searchCtrl.clear();
                            prov.loadWasteBanks();
                          },
                        )
                      : null,
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                  filled: true,
                  fillColor: const Color(0xFFF9FAFB),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: BorderSide(color: Colors.grey.shade300),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: BorderSide(color: Colors.grey.shade200),
                  ),
                ),
                onSubmitted: (val) => prov.loadWasteBanks(search: val),
              ),
            ),

            Expanded(
              child: prov.isLoadingBanks
                  ? const Center(child: CircularProgressIndicator(color: AppTheme.primaryColor))
                  : prov.wasteBanks.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.recycling_outlined, size: 64, color: Colors.grey.shade300),
                              const SizedBox(height: 12),
                              const Text('Belum ada bank sampah terdaftar', style: TextStyle(color: AppTheme.textMuted)),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          color: AppTheme.primaryColor,
                          onRefresh: () => prov.loadWasteBanks(search: _searchCtrl.text),
                          child: ListView.separated(
                            padding: const EdgeInsets.all(16),
                            itemCount: prov.wasteBanks.length,
                            separatorBuilder: (context, index) => const SizedBox(height: 12),
                            itemBuilder: (context, index) {
                              final bank = prov.wasteBanks[index];
                              return _buildBankCard(bank);
                            },
                          ),
                        ),
            ),
          ],
        );
      },
    );
  }

  Widget _buildBankCard(WasteBank bank) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.03),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: AppTheme.primaryColor.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Icon(Icons.recycling_rounded, color: AppTheme.primaryColor, size: 24),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      bank.name,
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.textDark),
                    ),
                    if (bank.villageName != null) ...[
                      const SizedBox(height: 4),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: AppTheme.secondaryColor.withValues(alpha: 0.2),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          bank.villageName!,
                          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF78350F)),
                        ),
                      ),
                    ],
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          if (bank.address != null) ...[
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(Icons.location_on_outlined, size: 16, color: AppTheme.textMuted),
                const SizedBox(width: 6),
                Expanded(
                  child: Text(
                    bank.address!,
                    style: const TextStyle(fontSize: 13, color: Color(0xFF4B5563)),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 6),
          ],
          if (bank.phone != null && bank.phone!.isNotEmpty) ...[
            Row(
              children: [
                const Icon(Icons.phone_rounded, size: 16, color: AppTheme.emphasis),
                const SizedBox(width: 6),
                Text(
                  bank.phone!,
                  style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppTheme.emphasis),
                ),
              ],
            ),
            const SizedBox(height: 6),
          ],
          if (bank.description != null && bank.description!.isNotEmpty) ...[
            Text(
              bank.description!,
              style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
            ),
          ],
          if (widget.onNavigateToMap != null) ...[
            const SizedBox(height: 10),
            Align(
              alignment: Alignment.centerRight,
              child: TextButton.icon(
                onPressed: () => widget.onNavigateToMap!(bank.latitude, bank.longitude),
                icon: const Icon(Icons.map_rounded, size: 16, color: AppTheme.primaryColor),
                label: const Text('Buka di Peta', style: TextStyle(color: AppTheme.primaryColor, fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildLandfillTab() {
    return Consumer<PublicProvider>(
      builder: (context, prov, child) {
        if (prov.isLoadingLandfills) {
          return const Center(child: CircularProgressIndicator(color: AppTheme.primaryColor));
        }

        if (prov.landfills.isEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.delete_sweep_outlined, size: 64, color: Colors.grey.shade300),
                const SizedBox(height: 12),
                const Text('Data TPA Sumbersari belum dimuat', style: TextStyle(color: AppTheme.textMuted)),
              ],
            ),
          );
        }

        return RefreshIndicator(
          color: AppTheme.primaryColor,
          onRefresh: () => prov.loadLandfills(),
          child: ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: prov.landfills.length,
            separatorBuilder: (context, index) => const SizedBox(height: 12),
            itemBuilder: (context, index) {
              final landfill = prov.landfills[index];
              return _buildLandfillCard(landfill);
            },
          ),
        );
      },
    );
  }

  Widget _buildLandfillCard(Landfill landfill) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.03),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF3C7),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Icon(Icons.delete_sweep_rounded, color: Color(0xFFB45309), size: 24),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      landfill.name,
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.textDark),
                    ),
                    const SizedBox(height: 4),
                    const Text('Tempat Pemrosesan Akhir (TPA)', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          if (landfill.address != null) ...[
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(Icons.location_on_outlined, size: 16, color: AppTheme.textMuted),
                const SizedBox(width: 6),
                Expanded(
                  child: Text(
                    landfill.address!,
                    style: const TextStyle(fontSize: 13, color: Color(0xFF4B5563)),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 6),
          ],
          if (landfill.description != null && landfill.description!.isNotEmpty) ...[
            Text(
              landfill.description!,
              style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
            ),
          ],
          if (widget.onNavigateToMap != null) ...[
            const SizedBox(height: 10),
            Align(
              alignment: Alignment.centerRight,
              child: TextButton.icon(
                onPressed: () => widget.onNavigateToMap!(landfill.latitude, landfill.longitude),
                icon: const Icon(Icons.map_rounded, size: 16, color: AppTheme.primaryColor),
                label: const Text('Buka di Peta', style: TextStyle(color: AppTheme.primaryColor, fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ],
      ),
    );
  }
}
