#!/usr/bin/env python
import argparse
import numpy as np

def SVtable(args):
    filename = args.file
    simplify_types = False #args.simplify
    f=open(filename)
    typeList = []
    sizeList = []
    rawTypes = []
    for line in f:
        fields = line.strip().split()
        svType = fields[6]
        rawTypes.append(svType)
        if simplify_types == True:
            if svType == "Insertion" or svType == "Expansion":
                typeList.append("Insertion/Expansion")
            elif svType == "Deletion" or svType == "Contraction":
                typeList.append("Deletion/Contraction")
            else:
                typeList.append(svType)
        else:
            typeList.append(svType)
        sizeList.append(float(fields[4]))
    f.close()
    
    sizeArray = np.array(sizeList)
    typeArray = np.array(typeList)
    svTypes = ["Insertion","Deletion","Tandem_expansion","Tandem_contraction","Repeat_expansion","Repeat_contraction"] #,"Substitution", "Longrange", "Interchromosomal"]
    if simplify_types == True:
        svTypes = ["Insertion/Expansion","Deletion/Contraction"] #,"Substitution", "Longrange", "Interchromosomal"]
    overall_total = 0
    overall_total_bases = 0
    for svType in svTypes:
        sizes = sizeArray[typeArray==svType]
        overall_total += len(sizes)
        overall_total_bases += sum(sizes)
        print svType
        print "\t\t\t\tCount\t\tTotal bp"
        print "\tTotal: \t\t\t", len(sizes), "\t\t", sum(sizes)
        # subset = sizes[sizes<50];                              print "\t1-49 bp: \t\t", len(subset), "\t\t", sum(subset)
        subset = sizes[np.logical_and(sizes>=50,sizes<100)];    print "\t50-99 bp: \t\t", len(subset),"\t\t", sum(subset)
        subset = sizes[np.logical_and(sizes>=100,sizes<1000)];    print "\t100-999 bp: \t\t", len(subset),"\t\t", sum(subset)
        subset = sizes[np.logical_and(sizes>=1000,sizes<10000)];    print "\t1000-9,999 bp: \t\t", len(subset), "\t\t", sum(subset)
        # subset = sizes[sizes>=10000];    print "\t> 10,000 bp: \t\t", len(subset), "\t\t", sum(subset)
        print "\n"
   
    for svType in list(set(rawTypes)-set(svTypes)):
        sizes = sizeArray[typeArray==svType]
        overall_total += len(sizes)
        overall_total_bases += sum(sizes)
        print svType
        print "\t\t\t\tCount\t\tTotal bp"
        print "\tTotal: \t\t\t", len(sizes), "\t\t", sum(sizes)
        # subset = sizes[sizes<50];                              print "\t1-49 bp: \t\t", len(subset), "\t\t", sum(subset)
        subset = sizes[np.logical_and(sizes>=50,sizes<=100)];    print "\t50-99 bp: \t\t", len(subset),"\t\t", sum(subset)
        subset = sizes[np.logical_and(sizes>=100,sizes<1000)];    print "\t100-999 bp: \t\t", len(subset),"\t\t", sum(subset)
        subset = sizes[np.logical_and(sizes>=1000,sizes<10000)];    print "\t1000-9,999 bp: \t\t", len(subset), "\t\t", sum(subset)
        # subset = sizes[sizes>=10000];    print "\t> 10,000 bp: \t\t", len(subset), "\t\t", sum(subset)
        print "\n"

    print "Total number of variants:", overall_total, "\n"
    print "Total affected bases:", overall_total_bases, "\n"


def main():
    parser=argparse.ArgumentParser(description='Output a summary table of variants from assembly-based variant-calling with mummer and svfinder',formatter_class=argparse.ArgumentDefaultsHelpFormatter)
    parser.add_argument('-i',help='bed file produced by svfinder.pl script',dest='file',type=str,required=True)
    # parser.add_argument('-simplify',help='Lump together Insertion/Expansion and Deletion/Contraction',dest='simplify',action='store_true')
    args=parser.parse_args()
    SVtable(args)
    
if __name__=="__main__":
    main()











