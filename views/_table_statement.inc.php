

                <tr class="table-dark no-hover bg-dark">
                    <th scope="col" class="bg-dark center-cell">&nbsp;</th>
                    <?php foreach ($compareFiles as $compareFile) { ?>
                    <th scope="col" class="bg-dark center-cell">&nbsp;</th>
                    <?php } ?>
                </tr>
                    <tr>
                        <th scope="row" rowspan="4" class="w-10">
                            Exec Count
                        </th>
                        <td>123</td>
                        <?php foreach ($compareFilesData as $compareFileData) { ?>
                            <td>321</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>Total Latency</td>
                        <td class="number-cell">13434234</td>
                        <?php foreach ($compareFilesData as $compareFileData) { ?>
                        <td class="number-cell right-border">s123123</td>
                        <?php } ?>
                    </tr>


